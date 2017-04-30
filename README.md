# 百度音乐采集（乐府令）
百度音乐的页面一直在改动，这套脚本估计现在也用不了了。

1. SQL脚本：./data/yuefuling.sql


2. 采集脚本：配置域名到admin目录下：

    2.1. 修改host文件：collect.music.com  D:/wwwroot/baidu_music/admin/
    
    2.2. 配置数据库：/baidu_music/admin/config/config.php
    
    2.3. 开始采集： http://collect.music.com 按1、2、3、4的顺序进行采集


3. 展示页面：配置域名到根目录下

      3.1. 修改host文件：show.music.com  D:/wwwroot/baidu_music/
      
      3.2. 配置数据库：/baidu_music/App/Conf/db.php
      
      3.3. 浏览器查看效果：http://show.music.com
