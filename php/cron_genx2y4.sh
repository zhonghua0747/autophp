cd /www/web/php

## 3 页面生成
## 3.1 生成公共页面
##./timeexc.sh curl http://127.0.0.1/php/genx2y4/gen_share.php -o ./log/x2y4genshare.log
## 3.4 生成index
./timeexc.sh curl http://127.0.0.1/php/genx2y4/gen_index.php -o ./log/x2y4genindex.log
## 3.2 生成pages
./timeexc.sh curl http://127.0.0.1/php/genx2y4/gen_page.php?d=30 -o ./log/x2y4genpage.log
## 3.3 生成list
./timeexc.sh curl http://127.0.0.1/php/genx2y4/gen_list.php -o ./log/x2y4genlist.log
## 3.5 生成辅助页面
./timeexc.sh curl http://127.0.0.1/php/genx2y4/gen_static.php -o ./log/x2y4genstatic.log
## 3.6 生成sitemap
./timeexc.sh curl http://127.0.0.1/php/genx2y4/gen_sitemap.php -o ./log/x2y4gensitemap.log
./timeexc.sh curl http://127.0.0.1/php/genx2y4/gen_search.php -o ./log/x2y4gensearch.log
