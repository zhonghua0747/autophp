cd /www/web/php

logfile=./run.log
rm -rf $logfile
echo $(date) > $logfile
## 1 资源获取
## 1.1从rss获取的资源
./timeexc.sh curl 'http://127.0.0.1/php/reader/getrss.php?fid=1&lid=40' -o ./log/getrss1.log
./timeexc.sh curl 'http://127.0.0.1/php/reader/getrss.php?fid=41&lid=80' -o ./log/getrss2.log
./timeexc.sh curl 'http://127.0.0.1/php/reader/getrss.php?fid=81&lid=1000' -o ./log/getrss3.log
## 1.2使用代码从(资源)获取的资源
./timeexc.sh curl http://127.0.0.1/php/reader/c1000/get.php -o ./log/c1000.log
## 1.3使用代码从(纯链接)获取的资源
./timeexc.sh curl http://127.0.0.1/php/reader/c2000/get.php -o ./log/c2000.log

## 2 资源处理
## 2.1 处理onlylink，得到mtitle
./timeexc.sh curl http://127.0.0.1/php/getpage/getonlylink.php?d=10 -o ./log/getonlylink.log
## 2.1 处理onlylink，将onlylink处理到link和link2
./timeexc.sh curl 'http://127.0.0.1/php/getpage/getlink2.php?f1=0&f2=2&r=0.9&d=10' -o ./log/getlink1.log
./timeexc.sh curl 'http://127.0.0.1/php/getpage/getlink2.php?f1=3&f2=5&r=0.4&d=10' -o ./log/getlink2.log
## 2.2 处理电影的状态，正在上映和即将上映的
./timeexc.sh curl http://127.0.0.1/php/getpage/upmstatus.php -o ./log/upmstatus.log
## 2.4 利用onlylink得到page并修改其状态
./timeexc.sh curl http://127.0.0.1/php/getpage/getpage.php?d=2 -o ./log/getpage.log
## 2.4 更新page的其他属性
./timeexc.sh curl http://127.0.0.1/php/getpage/updatepage.php?d=2 -o ./log/getpage.log
## 2.5 更新page的状态，资源数等
./timeexc.sh curl http://127.0.0.1/php/getpage/updatestatus.php?d=60 -o ./log/updatestatus.log
## 2.5 得到票房信息
##/srv/movie002/timeexc.sh curl http://127.0.0.1/php/reader/movie.mtime.com.boxoffice.php -o ./log/boxoffice.log
## 2.5 得到演员信息
./timeexc.sh curl http://127.0.0.1/php/reader/getcele/getcele.php -o ./log/getcele.log
##/srv/movie002/timeexc.sh  curl http://127.0.0.1/php/reader/getcele/getimgurl.php -o ./log/getimgurl.log

## 4 通知更新
./timeexc.sh curl http://127.0.0.1/php/genv/gen_update.php -o ./log/genupdate.log
echo $(date) >> $logfile

## 4 操作日志发送到邮箱
curl http://127.0.0.1/php/log/back_log.php
