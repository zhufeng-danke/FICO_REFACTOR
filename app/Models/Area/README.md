# 地域树的 Models

现有地域树是存储在 areas 表中，之前通过 Area 类来进行存取，
但创建 Relation 或检索时非常不方便，所以在这里拆分成了三个 Models ：

下面子项是对应 Model 中已有的 Relation

- City  城市
  - districts
  - blocks
- District  行政区
  - city
  - blocks
- Block 商圈
  - district