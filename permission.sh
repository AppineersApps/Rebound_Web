mkdir -p application/cache;
mkdir -p application/cache/js;
mkdir -p application/cache/styles;
mkdir -p application/cache/smarty;
mkdir -p application/cache/queries;
mkdir -p application/cache/js/compiled;
mkdir -p application/cache/styles/compiled;
mkdir -p application/cache/smarty/compiled;

chmod -Rf 777 application/cache;
chmod -Rf 777 application/cache/*;
chmod -Rf 777 application/language;
chmod -Rf 777 application/language/*;
chmod -Rf 777 application/front/content/views/static;
chmod -Rf 777 application/front/content/views/static/*;
chmod -Rf 777 public/access_log;
chmod -Rf 777 public/parse_log;
chmod -Rf 777 public/backup;
chmod -Rf 777 public/upload;
chmod -Rf 777 public/upload/*;