<?php
/**
 * User: zhufeng@dankegongyu.com
 * Date: 17/7/28
 * Time: 上午9:35
 */

namespace App\Http\Controllers\Admin\Traits\BI;

use App\Http\Controllers\Admin\Traits\QnSdk;
use App\Models\BI\GeneralRentInformationCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Tracking\HouseResource;
use Validator;

trait RapidAssessment
{
    use QnSdk;

    protected function floorList()
    {
        return [
            'L3' => '正常楼层',
            'L1' => '爬楼5层及以上',
            'L2' => '爬楼1层'
        ];
    }

    protected function roomStatusList()
    {
        return [
            'R3' => '老旧/毛坯',
            'R2' => '简装',
            'R1' => '精装'
        ];
    }

    protected function enviormentLevelList()
    {
        return [
            'N1' => '安静卫生',
            'N2' => '吵闹脏乱'
        ];
    }

    protected function sourceList()
    {
        return ['个人评估', '渠道获取', '链家网', '自如网', '我爱我家网', '58赶集网', '家家顺', '豪世华邦网', '其它网站'];
    }

    /**
     * 获取图片Urls
     * @param $rent_info_id
     * @return array
     */
    public function rentInformationUrls($rent_info_id)
    {
        $urls = [];

        $info = GeneralRentInformationCollection::find($rent_info_id);
        if (!$info || !$info->picture) {

            return $urls;
        }

        $picture = json_decode($info->picture);
        if (count($picture)) {
            foreach ($picture as $pic) {
                $urls[$pic->key] = $this->qnDownLoadFile($pic->key, $pic->mode);
            }
        }

        return $urls;
    }

    /**
     * 删除文件：
     *     1、七牛file；
     *     2、rentinfo => picture；
     *
     * @param $rent_info_id
     * @param $key
     * @return bool
     */
    public function deleteRentInfoFile($rent_info_id, $key)
    {
        $rentInfo = GeneralRentInformationCollection::find($rent_info_id);
        if (!$rentInfo || !$rentInfo->picture) {
            return false;
        }

        $keyInfo = [];
        $keys = json_decode($rentInfo->picture);
        foreach ($keys as $k => $v) {
            if ($v->key == $key) {
                unset($keys[$k]);
                $keyInfo = [
                    'mode' => $v->mode,
                    'key' => $v->key
                ];
                break;
            }
        }

        if (!count($keyInfo)) {
            return false;
        }

        if ($this->qnDeleteFile($keyInfo['key'], $keyInfo['mode'])) {
            if (count($keys)) {
                GeneralRentInformationCollection::where('id', $rent_info_id)->update(['picture' => json_encode($keys)]);
            }
        }

        return true;
    }

    /**
     * 查询小区，所属区块
     * @param Request $request
     * @return mixed|string
     */
    public function anyQueryBlock(Request $request)
    {
        $requestData = $request->all();
        if (isset($requestData['xiaoqu_id']) && $xq = \Xiaoqu::find($requestData['xiaoqu_id'])) {
            return $xq->block;
        }

        return '';
    }

    /**
     * 查询小区
     * @param Request $request
     * @return array|string
     */
    public function anyQueryXiaoQu(Request $request)
    {
        $requestData = $request->all();
        if (isset($requestData['city']) && Cache::has($requestData['city'] . '_小区列表')) {

            return Cache::get($requestData['city'] . '_小区列表');
        }


        if (isset($requestData['city']) && $xq = \Xiaoqu::where('city', $requestData['city'])->where('name', 'not like',
                '%废%')->pluck('name', 'id')->toArray()
        ) {
            $data = [];
            foreach ($xq as $k => $v) {
                $xq = \Xiaoqu::find($k);
                $v = $v . ' - ' . ($xq->block ?? '-');

                $data[] = [
                    'k' => $k,
                    'v' => $v
                ];
            }
//            Cache::forever($requestData['city'] . '_小区列表', $data);
            Cache::put($requestData['city'] . '_小区列表', $data, 24 * 60);

            return $data;
        }

        return '';
    }

    public function anyXiaoQu()
    {
        return \Xiaoqu::where('name', 'not like', '%废%')->pluck('name', 'id')->toArray();
    }

    /**
     * 生成普租情报列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     *
     * 实例：
     *     删除文件实例： $this->deleteRentInfoFile(3, 'fico-forecast-rent-info-597ed88699036');
     *     获取图片urls实例：$this->rentInformationUrls(3);
     */
    public function anyCreateInfo(Request $request)
    {
//        $this->assertCan('BI_普租情报_新增');
        $mode = 'public';//文件存储模式
        $title = '提交情报';
        $record = $data = [];
        $user = \Auth::user();

        $info = $request->all();
        if (isset($info['_token'])) {
            unset($info['_token']);
        }

        // 提交信息
        if ($request->method() == 'POST') {

            $validator = Validator::make($request->all(), [
                'city' => 'required',
                'xiaoqu_name' => 'required',
                'building_code' => 'required',
                'floor' => 'required',
                'area' => 'required|integer',
                'bedroom_num' => 'required|integer',
                'bef_gw' => 'required|integer',
                'bef_dw' => 'required|integer',
                'room_status' => 'required',
                'enviorment_level' => 'required',
                'source' => 'required',
                'sale_price' => 'required|integer',
            ]);


            if ($validator->fails()) {
                flash()->error('输入不满足要求，请重新输入。');
                return redirect()->back();
            }

            $xiaoqu = stripos($info['xiaoqu_name'], ' - ');
            if (!$xiaoqu) {
                flash()->error('请选择小区');
                return redirect()->back();
            }
            $xiaoQuName = substr($info['xiaoqu_name'], 0, $xiaoqu);
            $info['xiaoqu_name'] = $xiaoQuName;
            $xiaoQu = \Xiaoqu::where('city', $info['city'])->where('name', $xiaoQuName)->first();
            $info['xiaoqu_id'] = $xiaoQu->id ?? '';

            // 处理上传
            $pictures = $request->file('picture');
            if (count($pictures)) {
                $fileList = [];
                foreach ($pictures as $picture) {
                    $path = $picture->path();
                    $upload = $this->qnUpLoadFile($path, 'fico-forecast-rent-info-');

                    if (!$upload['err']) {
                        $fileList[] = [
                            'mode' => $mode,
                            'key' => $upload['key']
                        ];
                    }
                }
                $data['picture'] = json_encode($fileList);
            }

            if (isset($info['id']) && $info['id']) {
                flash()->success('更新成功.');
                GeneralRentInformationCollection::where('id', $info['id'])->update($info);
                $record = GeneralRentInformationCollection::find($info['id']);
            } else {
                flash()->success('新增成功.');
                $info['user_id'] = $user->id ?? '';
                $info['create_time'] = date('Y-m-d H:i:s');
                $info['picture'] = $data['picture'] ?? null;
                GeneralRentInformationCollection::create($info);

//                return redirect()->back();
                return redirect(action('\App\Http\Controllers\Admin\BI\RiskEvaluationController@anyIndex'));
            }
            $record = $record->toArray();
        }

        // 关联信息
        $requestData = $request->all();
        if (isset($requestData['house_resource_id']) && $houseResource = HouseResource::find($requestData['house_resource_id'])) {

            // 小区信息
            $xiaoqu = $houseResource->xiaoqu;
            if ($xiaoqu) {
                $data['xiaoqu_name'] = $xiaoqu->name . ' - ' . $xiaoqu->block or null;
                $data['xiaoqu_id'] = $xiaoqu->id or null;
                $data['city'] = $xiaoqu->city or null;
                $data['lng'] = $xiaoqu->longitude or null;
                $data['lat'] = $xiaoqu->latitude or null;
                $data['block'] = $xiaoqu->block or null;
            }

            // 房源信息
            $data['bedroom_num'] = $houseResource->record_bedroom_num or null;
            $data['area'] = $houseResource->record_area or null;
            $data['bef_gw'] = $houseResource->record_toilet_num or null;
            $data['bef_dw'] = 0;
            $data['room_status'] = $houseResource->online_decorate == '精装' ? 'R1' : 'R2';
        }

        // 城市列表
        $cityList = \Area::listCity();//where('level', \Area::LEVEL_城市)->pluck('name', 'id')->toArray();

        // 商圈列表
//        $blockList = \Area::where('level', \Area::LEVEL_商圈)->pluck('name', 'id')->toArray();

        // 楼层列表
        $floorList = $this->floorList();

        // 装修情况列表
        $roomStatusList = $this->roomStatusList();

        // 环境情况列表
        $enviormentLevelList = $this->enviormentLevelList();

        // 价格来源渠道列表
        $sourceList = $this->sourceList();

        return view('admin.bi.risk-evaluation.create-info',
            compact('title', 'cityList', 'floorList', 'roomStatusList', 'enviormentLevelList',
                'sourceList', 'record', 'data'));
    }


}