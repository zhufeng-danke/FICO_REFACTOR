## ACL , Access Control List [权限控制系统]

### 分类
Permission =>  普通权限表
PermissionByArea => 按辖区分配的权限表
Role => 角色表  
CorpUserRole/PermissionRole是关系表  
一个CorpUser可以属于多个Role  
一个Role拥有不同Permission  

## 全局快捷方法

### role($role_name)

### can($permission_name)
算法： 根据 Permission->Roles & CorpUser->Roles 两组Roles求交集，有交集返回true

### CorpUser::canInArea(string $permission_name , Area $area)
一个用户在特定辖区是否拥有对应权限。

### CorpUser::whoIs($role_name)

### CorpUser::whoCan($permission_name)
