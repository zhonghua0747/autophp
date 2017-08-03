logfile=/web/php/log/run.log
rm -rf $logfile
echo $(date) > $logfile
## 3 页面生成
## 3.1 生成公共页面
##/web/php/timeexc.sh curl http://127.0.0.1/php/genx/gen_share.php -o /web/php/log/genshare.log
## 3.4 生成index
/web/php/timeexc.sh curl http://127.0.0.1/php/genx/gen_index.php -o /web/php/log/xgenindex.log
## 3.2 生成pages
/web/php/timeexc.sh curl http://127.0.0.1/php/genx/gen_page.php?d=30 -o /web/php/log/xgenpage.log
## 3.3 生成list
/web/php/timeexc.sh curl http://127.0.0.1/php/genx/gen_list.php -o /web/php/log/xgenlist.log
## 3.5 生成辅助页面
/web/php/timeexc.sh curl http://127.0.0.1/php/genx/gen_static.php -o /web/php/log/xgenstatic.log
## 3.6 生成sitemap
/web/php/timeexc.sh curl http://127.0.0.1/php/genx/gen_sitemap.php -o /web/php/log/xgensitemap.log
/web/php/timeexc.sh curl http://127.0.0.1/php/genx/gen_search.php -o /web/php/log/xgensearch.log
