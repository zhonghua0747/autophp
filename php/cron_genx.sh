cd /www/web/php

logfile=./log/run.log
rm -rf $logfile
echo $(date) > $logfile

## 3 页面生成
## 3.1 生成公共页面
##./timeexc.sh curl http://127.0.0.1/php/genx/gen_share.php -o ./log/genshare.log
## 3.4 生成index
./timeexc.sh curl http://127.0.0.1/php/genx/gen_index.php -o ./log/xgenindex.log
## 3.2 生成pages
./timeexc.sh curl http://127.0.0.1/php/genx/gen_page.php?d=30 -o ./log/xgenpage.log
## 3.3 生成list
./timeexc.sh curl http://127.0.0.1/php/genx/gen_list.php -o ./log/xgenlist.log
## 3.5 生成辅助页面
./timeexc.sh curl http://127.0.0.1/php/genx/gen_static.php -o ./log/xgenstatic.log
## 3.6 生成sitemap
./timeexc.sh curl http://127.0.0.1/php/genx/gen_sitemap.php -o ./log/xgensitemap.log
./timeexc.sh curl http://127.0.0.1/php/genx/gen_search.php -o ./log/xgensearch.log
