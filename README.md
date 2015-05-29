# zentao-redmine-userauth
做了一个基本的用户认证接口，兼容redmine

将代码pull下来，放到禅道根目录下的www目录中即可。

使用场景：

公司内部机器有限，找了一台很老的机器来跑Git服务 - 运行着晕倒死系统。最终选择了Gitblit，部署简单，用户认证机制可扩展。无意中看到了支持Redmine，思路来了：给禅道做了兼容接口，即可对接内部的禅道系统用户。

禅道部署好了以后，还需要修改Gitblit参数。

找到配置文件：gitblit.properties

```ini
realm.authenticationProviders = htpasswd redmine

realm.redmine.url = http://192.168.0.2/pms/redmine
```

注：禅道运行的目录如果/pms，还需要修改禅道这边接口的.htaccess文件。
