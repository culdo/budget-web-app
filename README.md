# 多功能記帳Web APP

<img src="https://user-images.githubusercontent.com/26900749/160599022-2602f870-7075-42f4-9ae9-2c8162d3c958.gif" width="80%"/>

> 這裡有DEMO錄影，檔案小肥請稍等
## Environment
* Ubuntu 16.04 & 18.04 (Tested)

## SQL table
```SQL
CREATE TABLE `lose_money` (
  `id` int(11) PRIMARY KEY AUTOINCREMENT,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `kind` varchar(10) NOT NULL DEFAULT '未分類',
  `image` varchar(100) NOT NULL DEFAULT 'https://www.jiuwa.net/tuku/20180728/E7AIP6Gu.gif',
  `name` varchar(50) NOT NULL DEFAULT '未知',
  `cost` int(11) NOT NULL,
  `info` varchar(100) NOT NULL
);
```
## Requirements
* Mysql
* PHPmyadmin
* Apach2