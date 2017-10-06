blog
====

Kurulum
==

Aşağıdaki işlemleri sırasıyla takip ediniz.

Bağımlıkları yüklemek için
```cli
composer install
```

Ubuntu ile nodejs kurulum

```cli
sudo apt-get install --yes nodejs
```

Bower kurulum

```cli
npm install -g bower
```

Grunt Kurulum

```cli
npm install -g grunt-cli
```

Jquery, bootstrap gibi kütüphanelerin kurulumu için

```cli
bower install
```

grunt ve bağlı paketlerin kurulumu için
```cli
npm install
```

Assets lerin ayarlanması için

```cli
grunt
```

Database oluşturmak için
```cli
php bin/console doctrine:database:create
```

Database schema oluşturmak için
```cli
php bin/console doctrine:schema:update --force
```

Örnek Kullanıcı eklemek için

Admin
```cli
php app/console app:add-user admin_user **admin_user** admin_user@example.com --is-admin',
```

Kullanıcı
```cli
php app/console app:add-user alisan_user **alisan_user** alisan_user@example.com',
```