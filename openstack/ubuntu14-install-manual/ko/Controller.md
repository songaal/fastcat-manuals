# 오픈스택 컨트롤러 노드

#### 네트워크 설정

**/etc/network/interfaces**

```
# Management
auto eth0
iface eth0 inet static
address 10.0.1.11
netmask 255.255.255.0
gateway 10.0.1.1
```

```ruby
reboot
```

**/etc/hosts**

```
# controller
10.0.1.11       controller

# network
10.0.1.21       network

# compute1
10.0.1.31       compute1

# block1
10.0.1.41       block1
```

#### Verify connectivity

```ruby
ping -c 4 openstack.org
ping -c 4 network
ping -c 4 compute1
ping -c 4 controller
ping -c 4 block1
```

## Network Time Protocol (NTP)
#### To install the NTP service

```ruby
apt-get install ntp
```

#### To configure the NTP service

**/etc/ntp.conf**

```
server time.bora.net iburst
restrict -4 default kod notrap nomodify
restrict -6 default kod notrap nomodify
```

```ruby
rm /var/lib/ntp/ntp.conf.dhcp

service ntp restart
```

#### Verify operation

```ruby
ntpq -c peers

ntpq -c assoc
```

#### 패스워드 사용하려면
```ruby
openssl rand -hex 10
>> c8c6fd0b91a39cf2024e
```

## OpenStack packages

#### To enable the OpenStack repository

```ruby
apt-get install ubuntu-cloud-keyring

echo "deb http://ubuntu-cloud.archive.canonical.com/ubuntu" "trusty-updates/kilo main" > /etc/apt/sources.list.d/cloudarchive-kilo.list
```

#### To finalize installation

```ruby
apt-get update && apt-get dist-upgrade
```

#### 한번에 모두 설치

```ruby
apt-get install -y mariadb-server python-mysqldb rabbitmq-server keystone python-openstackclient apache2 libapache2-mod-wsgi memcached python-memcache glance python-glanceclient nova-api nova-cert nova-conductor nova-consoleauth nova-novncproxy nova-scheduler python-novaclient neutron-server neutron-plugin-ml2 python-neutronclient openstack-dashboard cinder-api cinder-scheduler python-cinderclient openstack-dashboard
```

## SQL database
#### To install and configure the database server

```ruby
apt-get install mariadb-server python-mysqldb
```

**/etc/mysql/conf.d/mysqld_openstack.cnf**

```
[mysqld]
bind-address = %CONTROLLER_NODE_MANAGEMENT_IP%

[mysqld]
default-storage-engine = innodb
innodb_file_per_table
collation-server = utf8_general_ci
init-connect = 'SET NAMES utf8'
character-set-server = utf8
```

```ruby
service mysql restart

mysql_secure_installation

Secure the database service:  >>> 묻는 것에는 모두 Y로.
```

## Message queue

#### To install the message queue service

```ruby
apt-get install rabbitmq-server

rabbitmqctl add_user openstack rabbitpass

rabbitmqctl set_permissions openstack ".*" ".*" ".*"
```

## Identity service (keystone) - Controller Node Only

#### To configure prerequisites

```ruby
$ mysql -u root -p
CREATE DATABASE keystone;
GRANT ALL PRIVILEGES ON keystone.* TO 'keystone'@'localhost' IDENTIFIED BY 'keystonedbpass';
GRANT ALL PRIVILEGES ON keystone.* TO 'keystone'@'%' IDENTIFIED BY 'keystonedbpass';
```

#### To install and configure the Identity service components
```ruby
echo "manual" > /etc/init/keystone.override

apt-get install keystone python-openstackclient apache2 libapache2-mod-wsgi memcached python-memcache -y
```

***/etc/keystone/keystone.conf***

```
[DEFAULT]
verbose = True
admin_token = ab1670a916b0ef3ed39b

[database]
connection = mysql://keystone:keystonedbpass@controller/keystone

[memcache]
servers = localhost:11211

[token]
provider = keystone.token.providers.uuid.Provider
driver = keystone.token.persistence.backends.memcache.Token

[revoke]
driver = keystone.contrib.revoke.backends.sql.Revoke
```

```ruby
su -s /bin/sh -c "keystone-manage db_sync" keystone
```

#### To configure the Apache HTTP server

**/etc/apache2/apache2.conf**

```
ServerName controller
```

**/etc/apache2/sites-available/wsgi-keystone.conf**

```
Listen 5000
Listen 35357
<VirtualHost *:5000>
    WSGIDaemonProcess keystone-public processes=5 threads=1 user=keystone display-name=%{GROUP}
    WSGIProcessGroup keystone-public
    WSGIScriptAlias / /var/www/cgi-bin/keystone/main
    WSGIApplicationGroup %{GLOBAL}
    WSGIPassAuthorization On
    <IfVersion >= 2.4>
      ErrorLogFormat "%{cu}t %M"
    </IfVersion>
    LogLevel info
    ErrorLog /var/log/apache2/keystone-error.log
    CustomLog /var/log/apache2/keystone-access.log combined
</VirtualHost>
<VirtualHost *:35357>
    WSGIDaemonProcess keystone-admin processes=5 threads=1 user=keystone display-name=%{GROUP}
    WSGIProcessGroup keystone-admin
    WSGIScriptAlias / /var/www/cgi-bin/keystone/admin
    WSGIApplicationGroup %{GLOBAL}
    WSGIPassAuthorization On
    <IfVersion >= 2.4>
      ErrorLogFormat "%{cu}t %M"
    </IfVersion>
    LogLevel info
    ErrorLog /var/log/apache2/keystone-error.log
    CustomLog /var/log/apache2/keystone-access.log combined
</VirtualHost>
```

```ruby
ln -s /etc/apache2/sites-available/wsgi-keystone.conf /etc/apache2/sites-enabled

mkdir -p /var/www/cgi-bin/keystone

curl http://git.openstack.org/cgit/openstack/keystone/plain/httpd/keystone.py?h=stable/kilo | tee /var/www/cgi-bin/keystone/main /var/www/cgi-bin/keystone/admin

chown -R keystone:keystone /var/www/cgi-bin/keystone

chmod 755 /var/www/cgi-bin/keystone/*
```


#### To finalize installation
```ruby
service apache2 restart

rm -f /var/lib/keystone/keystone.db
```


## Create the service entity and API endpoint
#### To configure prerequisites
```ruby
$
export OS_TOKEN=ab1670a916b0ef3ed39b
export OS_URL=http://controller:35357/v2.0
```

#### To create the service entity and API endpoint
```ruby
$
openstack service create --name keystone --description "OpenStack Identity" identity

openstack endpoint create \
  --publicurl http://controller:5000/v2.0 \
  --internalurl http://controller:5000/v2.0 \
  --adminurl http://controller:35357/v2.0 \
  --region RegionOne \
  identity
```

## Create projects, users, and roles
#### To create tenants, users, and roles
```ruby
$
openstack project create --description "Admin Project" admin

openstack user create --password-prompt admin
패스워드 : adminpass
```
```ruby
$
openstack role create admin

openstack role add --project admin --user admin admin
```

#### Service Project
```ruby
$
openstack project create --description "Service Project" service

openstack project create --description "Demo Project" demo

openstack user create --password-prompt demo
패스워드 : demopass
```
```ruby
$
openstack role create user

openstack role add --project demo --user demo user
```

#### Verify operation

- For security reasons, disable the temporary authentication token mechanism
- Edit the `/etc/keystone/keystone-paste.ini` file and remove `admin_token_auth` from the `[pipeline:public_api]`, `[pipeline:admin_api]`, and `[pipeline:api_v3]` sections.

```ruby
$ unset OS_TOKEN OS_URL
```

```ruby
$
openstack --os-auth-url http://controller:35357 --os-project-name admin --os-username admin --os-auth-type password token issue

openstack --os-auth-url http://controller:35357 --os-project-domain-id default --os-user-domain-id default --os-project-name admin --os-username admin --os-auth-type password token issue

openstack --os-auth-url http://controller:35357 --os-project-name admin --os-username admin --os-auth-type password project list

openstack --os-auth-url http://controller:35357 --os-project-name admin --os-username admin --os-auth-type password user list

openstack --os-auth-url http://controller:35357 --os-project-name admin --os-username admin --os-auth-type password role list

openstack --os-auth-url http://controller:5000 --os-project-domain-id default --os-user-domain-id default --os-project-name demo --os-username demo --os-auth-type password token issue

openstack --os-auth-url http://controller:5000 --os-project-domain-id default --os-user-domain-id default --os-project-name demo --os-username demo --os-auth-type password user list
> ERROR: openstack You are not authorized to perform the requested action, admin_required. (HTTP 403
```

## Create OpenStack client environment scripts
#### To create the scripts

**admin-openrc.sh**

```
export OS_PROJECT_DOMAIN_ID=default
export OS_USER_DOMAIN_ID=default
export OS_PROJECT_NAME=admin
export OS_TENANT_NAME=admin
export OS_USERNAME=admin
export OS_PASSWORD=adminpass #ADMIN_PASS
export OS_PASSWORD_TYPE=password #내가 추가함.
export OS_AUTH_URL=http://controller:35357/v3
```

**demo-openrc.sh**

```
export OS_PROJECT_DOMAIN_ID=default
export OS_USER_DOMAIN_ID=default
export OS_PROJECT_NAME=demo
export OS_TENANT_NAME=demo
export OS_USERNAME=demo
export OS_PASSWORD=demopass # DEMO_PASS
export OS_PASSWORD_TYPE=password #내가 추가함.
export OS_AUTH_URL=http://controller:5000/v3
```

#### To load client environment scripts

```ruby
$
source admin-openrc.sh

openstack token issue
```

## Image service (Glance) - Controller Node Only
#### To configure prerequisites

```ruby
$ mysql -u root -p
CREATE DATABASE glance;
GRANT ALL PRIVILEGES ON glance.* TO 'glance'@'localhost' IDENTIFIED BY 'glancedbpass';
GRANT ALL PRIVILEGES ON glance.* TO 'glance'@'%' IDENTIFIED BY 'glancedbpass';
```

```ruby
$
source admin-openrc.sh

openstack user create --password-prompt glance
패스워드 : glancepass
```
```ruby
$
openstack role add --project service --user glance admin

openstack service create --name glance --description "OpenStack Image service" image

openstack endpoint create \
  --publicurl http://controller:9292 \
  --internalurl http://controller:9292 \
  --adminurl http://controller:9292 \
  --region RegionOne \
  image
```

#### To install and configure the Image service components

```ruby
apt-get install glance python-glanceclient
```

**/etc/glance/glance-api.conf**

```
[DEFAULT]
notification_driver = noop
verbose = True

[database]
connection = mysql://glance:glancedbpass@controller/glance

[keystone_authtoken]
#Comment out or remove any other options in the [keystone_authtoken]section.
auth_uri = http://controller:5000/v2.0
auth_url = http://controller:35357
auth_plugin = password
project_domain_id = default
user_domain_id = default
project_name = service
username = glance
password = glancepass

[paste_deploy]
flavor = keystone

[glance_store]
default_store = file
filesystem_store_datadir = /var/lib/glance/images/
```

**/etc/glance/glance-registry.conf**

```
[DEFAULT]
notification_driver = noop
verbose = True

[database]
connection = mysql://glance:glancedbpass@controller/glance

[keystone_authtoken]
#Comment out or remove any other options in the [keystone_authtoken]section.
auth_uri = http://controller:5000/v2.0
auth_url = http://controller:35357
auth_plugin = password
project_domain_id = default
user_domain_id = default
project_name = service
username = glance
password = glancepass

[paste_deploy]
flavor = keystone
```

```ruby
su -s /bin/sh -c "glance-manage db_sync" glance
```
```ruby
service glance-registry restart

service glance-api restart

rm -f /var/lib/glance/glance.sqlite
```

#### Verify operation

```ruby
$ echo "export OS_IMAGE_API_VERSION=2" | tee -a admin-openrc.sh demo-openrc.sh
```
```ruby
$
source admin-openrc.sh

mkdir /tmp/images

wget -P /tmp/images http://download.cirros-cloud.net/0.3.4/cirros-0.3.4-x86_64-disk.img

glance image-create --name "cirros-0.3.4-x86_64" --file /tmp/images/cirros-0.3.4-x86_64-disk.img --disk-format qcow2 --container-format bare --visibility public --progress
```

- Image CLI : http://docs.openstack.org/cli-reference/content/glanceclient_commands.html#glanceclient_subcommand_image-create
- More official images : http://docs.openstack.org/image-guide/obtain-images.html#official-ubuntu-images

> ubuntu14에서 부팅이 느릴때 해결법
> /etc/cloud/cloud.cfg.d/90_dpkg.cfg
> datasource_list: [ OpenStack ]

```ruby
$ glance image-list
```
```ruby
$ rm -r /tmp/images
```

## Compute service - with Compute Node
### Install and configure controller node
#### To configure prerequisites

```ruby
$ mysql -u root -p
CREATE DATABASE nova;
GRANT ALL PRIVILEGES ON nova.* TO 'nova'@'localhost' IDENTIFIED BY 'novadbpass';
GRANT ALL PRIVILEGES ON nova.* TO 'nova'@'%' IDENTIFIED BY 'novadbpass';
```

```ruby
$
source admin-openrc.sh
openstack user create --password-prompt nova
```

```ruby
$
openstack role add --project service --user nova admin
openstack service create --name nova   --description "OpenStack Compute" compute
openstack endpoint create \
  --publicurl http://controller:8774/v2/%\(tenant_id\)s \
  --internalurl http://controller:8774/v2/%\(tenant_id\)s \
  --adminurl http://controller:8774/v2/%\(tenant_id\)s \
  --region RegionOne \
  compute
```

#### To install and configure Compute controller components
```ruby
apt-get install nova-api nova-cert nova-conductor nova-consoleauth nova-novncproxy nova-scheduler python-novaclient
```

**/etc/nova/nova.conf**

> `auth_uri` 설정이 매뉴얼에는 `auth_uri = http://controller:5000`로 되어있지만, 접속이 자주 끊기는 현상이 자주발생하므로 `v2.0` 을 붙여서 `auth_uri = http://controller:5000/v2.0` 와 같이 버전을 명시하도록 한다.

```
[database]
connection = mysql://nova:novadbpass@controller/nova

[DEFAULT]
rpc_backend = rabbit
auth_strategy = keystone
my_ip = %CONTROLLER_NODE_MANAGEMENT_IP%
vncserver_listen = %CONTROLLER_NODE_MANAGEMENT_IP%
vncserver_proxyclient_address = %CONTROLLER_NODE_MANAGEMENT_IP%
verbose = True

[oslo_messaging_rabbit]
rabbit_host = controller
rabbit_userid = openstack
rabbit_password = rabbitpass

[keystone_authtoken]
#Comment out or remove any other options in the [keystone_authtoken]section.
auth_uri = http://controller:5000/v2.0
auth_url = http://controller:35357
auth_plugin = password
project_domain_id = default
user_domain_id = default
project_name = service
username = nova
password = novapass

[glance]
host = controller

[oslo_concurrency]
lock_path = /var/lib/nova/tmp
```

```ruby
su -s /bin/sh -c "nova-manage db sync" nova
```

#### To finalize installation

```ruby
service nova-api restart
service nova-cert restart
service nova-consoleauth restart
service nova-scheduler restart
service nova-conductor restart
service nova-novncproxy restart
rm -f /var/lib/nova/nova.sqlite
```

#### 여기서 compute node 쪽을 설치하고 온다.

#### Verify operation
```ruby
$
source admin-openrc.sh
nova service-list
nova endpoints
nova image-list
```

## OpenStack Networking (neutron)

### Install and configure controller node
#### To configure prerequisites

```ruby
$ mysql -u root -p
CREATE DATABASE neutron;
GRANT ALL PRIVILEGES ON neutron.* TO 'neutron'@'localhost' IDENTIFIED BY 'neutrondbpass';
GRANT ALL PRIVILEGES ON neutron.* TO 'neutron'@'%' IDENTIFIED BY 'neutrondbpass';
```
```ruby
$
source admin-openrc.sh

openstack user create --password-prompt neutron
```

```ruby
$
openstack role add --project service --user neutron admin

openstack service create --name neutron --description "OpenStack Networking" network

openstack endpoint create \
  --publicurl http://controller:9696 \
  --adminurl http://controller:9696 \
  --internalurl http://controller:9696 \
  --region RegionOne \
  network
```
#### To install the Networking components
```ruby
apt-get install neutron-server neutron-plugin-ml2 python-neutronclient
```

#### To configure the Networking server component

**/etc/neutron/neutron.conf**

```
[DEFAULT]
verbose = True
rpc_backend = rabbit
auth_strategy = keystone
core_plugin = ml2
service_plugins = router
allow_overlapping_ips = True
notify_nova_on_port_status_changes = True
notify_nova_on_port_data_changes = True
nova_url = http://controller:8774/v2

[database]
connection = mysql://neutron:neutrondbpass@controller/neutron

[oslo_messaging_rabbit]
rabbit_host = controller
rabbit_userid = openstack
rabbit_password = rabbitpass

[keystone_authtoken]
#Comment out or remove any other options in the [keystone_authtoken]section.
auth_uri = http://controller:5000/v2.0
auth_url = http://controller:35357
auth_plugin = password
project_domain_id = default
user_domain_id = default
project_name = service
username = neutron
password = neutronpass

[nova]
auth_url = http://controller:35357
auth_plugin = password
project_domain_id = default
user_domain_id = default
region_name = RegionOne
project_name = service
username = nova
password = novapass
```

#### To configure the Modular Layer 2 (ML2) plug-in

**/etc/neutron/plugins/ml2/ml2_conf.ini**

```
[ml2]
type_drivers = flat,vlan,gre,vxlan
tenant_network_types = gre
mechanism_drivers = openvswitch

[ml2_type_gre]
tunnel_id_ranges = 1:1000

[securitygroup]
enable_security_group = True
enable_ipset = True
firewall_driver = neutron.agent.linux.iptables_firewall.OVSHybridIptablesFirewallDriver
```

#### To configure Compute to use Networking
**/etc/nova/nova.conf**

```
[DEFAULT]
network_api_class = nova.network.neutronv2.api.API
security_group_api = neutron
linuxnet_interface_driver = nova.network.linux_net.LinuxOVSInterfaceDriver
firewall_driver = nova.virt.firewall.NoopFirewallDriver

[neutron]
url = http://controller:9696
auth_strategy = keystone
admin_auth_url = http://controller:35357/v2.0
admin_tenant_name = service
admin_username = neutron
admin_password = neutronpass
```

#### To finalize installation

```ruby
su -s /bin/sh -c "neutron-db-manage --config-file /etc/neutron/neutron.conf --config-file /etc/neutron/plugins/ml2/ml2_conf.ini upgrade head" neutron
```

> Database population occurs later for Networking because the script requires complete server and plug-in configuration files

```ruby
service nova-api restart

service neutron-server restart

rm -f /var/lib/neutron/neutron.sqlite
```

#### Verify operation

```ruby
$
source admin-openrc.sh

neutron ext-list
```

#### 이제 Network 노드를 설정한다.

#### Network 노드를 설정한뒤에 해야할일

**/etc/nova/nova.conf**

```
[neutron]
service_metadata_proxy = True
metadata_proxy_shared_secret = c8c6fd0b91a39cf2024e
```
```ruby
service nova-api restart
```

#### 다시 Network 노드 설정으로 복귀한다.

#### Verify operation

```ruby
$
source admin-openrc.sh

neutron agent-list
```

## Create Initial Network

### External network

#### To create the external network

```ruby
$ source admin-openrc.sh

$ neutron net-create ext-net --router:external \
  --provider:physical_network external --provider:network_type flat

+---------------------------+--------------------------------------+
| Field                     | Value                                |
+---------------------------+--------------------------------------+
| admin_state_up            | True                                 |
| id                        | 893aebb9-1c1e-48be-8908-6b947f3237b3 |
| name                      | ext-net                              |
| provider:network_type     | flat                                 |
| provider:physical_network | external                             |
| provider:segmentation_id  |                                      |
| router:external           | True                                 |
| shared                    | False                                 |
| status                    | ACTIVE                               |
| subnets                   |                                      |
| tenant_id                 | 54cd044c64d5408b83f843d63624e0d8     |
+---------------------------+--------------------------------------+
```

#### To create a subnet on the external network
```ruby
$ neutron subnet-create ext-net EXTERNAL_NETWORK_CIDR --name ext-subnet \
  --allocation-pool start=FLOATING_IP_START,end=FLOATING_IP_END \
  --disable-dhcp --gateway EXTERNAL_NETWORK_GATEWAY
```
**For example)**

```ruby
$ neutron subnet-create ext-net 203.0.113.0/24 --name ext-subnet \
  --allocation-pool start=203.0.113.101,end=203.0.113.200 \
  --disable-dhcp --gateway 203.0.113.1
Created a new subnet:
+-------------------+------------------------------------------------------+
| Field             | Value                                                |
+-------------------+------------------------------------------------------+
| allocation_pools  | {"start": "203.0.113.101", "end": "203.0.113.200"}   |
| cidr              | 203.0.113.0/24                                       |
| dns_nameservers   |                                                      |
| enable_dhcp       | False                                                |
| gateway_ip        | 203.0.113.1                                          |
| host_routes       |                                                      |
| id                | 9159f0dc-2b63-41cf-bd7a-289309da1391                 |
| ip_version        | 4                                                    |
| ipv6_address_mode |                                                      |
| ipv6_ra_mode      |                                                      |
| name              | ext-subnet                                           |
| network_id        | 893aebb9-1c1e-48be-8908-6b947f3237b3                 |
| tenant_id         | 54cd044c64d5408b83f843d63624e0d8                     |
+-------------------+------------------------------------------------------+
```

## Tenant Network

#### To create the tenant network
```ruby
$
source demo-openrc.sh

neutron net-create demo-net
Created a new network:
```
```ruby
$ neutron subnet-create demo-net TENANT_NETWORK_CIDR \
  --name demo-subnet --dns-nameserver DNS_RESOLVER \
  --gateway TENANT_NETWORK_GATEWAY
```
**For Example)**

```ruby
$ neutron subnet-create demo-net 192.168.1.0/24 --name demo-subnet --dns-nameserver 8.8.4.4 --gateway 192.168.1.1
```

#### To create a router on the tenant network and attach the external and tenant networks to it
```ruby
$
neutron router-create demo-router

neutron router-interface-add demo-router demo-subnet

neutron router-gateway-set demo-router ext-net
```


#### Verify connectivity

Following the external network subnet example using 203.0.113.0/24, the tenant router gateway should occupy the lowest IP address in the floating IP address range, 203.0.113.101.
If you configured your external physical network and virtual networks correctly, you should be able to ping this IP address from any host on your external physical network.
If you are building your OpenStack nodes as virtual machines, you must configure the hypervisor to permit promiscuous mode on the external network.

```ruby
$ ping -c 4 203.0.113.101
PING 203.0.113.101 (203.0.113.101) 56(84) bytes of data.
64 bytes from 203.0.113.101: icmp_req=1 ttl=64 time=0.619 ms
```

## Launch Test
```ruby
$
source demo-openrc.sh

nova keypair-add demo-key

nova keypair-list
```

#### To launch an instance

```ruby
$
nova flavor-list

nova image-list

neutron net-list

nova secgroup-list
```

```ruby
$ nova boot --flavor m1.tiny --image cirros-0.3.4-x86_64 --nic net-id=DEMO_NET_ID \
  --security-group default --key-name demo-key demo-instance1
```
```ruby
$ nova list
```

#### To access your instance using a virtual console
```ruby
$ nova get-vnc-console demo-instance1 novnc
```

#### To access your instance remotely

```ruby
$
nova secgroup-add-rule default icmp -1 -1 0.0.0.0/0

nova secgroup-add-rule default tcp 22 22 0.0.0.0/0
```
```ruby
$ neutron floatingip-create ext-net
```
```ruby
$ nova floating-ip-associate demo-instance1 NEW_FLOATING_IP
```
```ruby
$ nova list
```
```ruby
$ ping -c 4 NEW_FLOATING_IP
```
```ruby
$ ssh cirros@NEW_FLOATING_IP
```

#### To attach a Block Storage volume to your instance

```ruby
$ source demo-openrc.sh
```
```ruby
$ nova volume-list
```
```ruby
$ nova volume-attach demo-instance1 158bea89-07db-4ac2-8115-66c0d6a4bb48
```
```ruby
$ nova volume-list
```
```ruby
$ ssh cirros@203.0.113.102
```
```ruby
$ sudo fdisk -l

   Device Boot      Start         End      Blocks   Id  System
/dev/vda1   *       16065     2088449     1036192+  83  Linux

Disk /dev/vdb: 1073 MB, 1073741824 bytes
```

> You must create a partition table and file system to use the volume.

## Dashboard (Horizon)
```ruby
# apt-get install openstack-dashboard
```

**/etc/openstack-dashboard/local_settings.py**

```
OPENSTACK_HOST = "controller"
ALLOWED_HOSTS = ['*', ]
CACHES = {
   'default': {
       'BACKEND': 'django.core.cache.backends.memcached.MemcachedCache',
       'LOCATION': '127.0.0.1:11211',
   }
}
#CACHES : Comment out any other session storage configuration.
OPENSTACK_KEYSTONE_DEFAULT_ROLE = "user"
TIME_ZONE = "Asia/Seoul"
```
```ruby
# service apache2 reload
```

Try access http://controller/horizon




##Block Storage (Cinder)

```ruby
$ mysql -u root -p
CREATE DATABASE cinder;
GRANT ALL PRIVILEGES ON cinder.* TO 'cinder'@'localhost' IDENTIFIED BY 'cinderdbpass';
GRANT ALL PRIVILEGES ON cinder.* TO 'cinder'@'%' IDENTIFIED BY 'cinderdbpass';
```

```ruby
$
source admin-openrc.sh

openstack user create --password-prompt cinder
User Password: cinderpass
Repeat User Password:
```
```ruby
$
openstack role add --project service --user cinder admin

openstack service create --name cinder --description "OpenStack Block Storage" volume

openstack service create --name cinderv2 --description "OpenStack Block Storage" volumev2

openstack endpoint create \
  --publicurl http://controller:8776/v2/%\(tenant_id\)s \
  --internalurl http://controller:8776/v2/%\(tenant_id\)s \
  --adminurl http://controller:8776/v2/%\(tenant_id\)s \
  --region RegionOne \
  volume

openstack endpoint create \
  --publicurl http://controller:8776/v2/%\(tenant_id\)s \
  --internalurl http://controller:8776/v2/%\(tenant_id\)s \
  --adminurl http://controller:8776/v2/%\(tenant_id\)s \
  --region RegionOne \
  volumev2
```

#### To install and configure Block Storage controller components

```ruby
# apt-get install cinder-api cinder-scheduler python-cinderclient
```

**/etc/cinder/cinder.conf**

```
[DEFAULT]
verbose = True
rpc_backend = rabbit
my_ip = %CONTROLLER_NODE_MANAGEMENT_IP%
auth_strategy = keystone

[database]
connection = mysql://cinder:cinderdbpass@controller/cinder

[oslo_messaging_rabbit]
rabbit_host = controller
rabbit_userid = openstack
rabbit_password = rabbitpass

[keystone_authtoken]
#Comment out or remove any other options in the [keystone_authtoken]section.
auth_uri = http://controller:5000
auth_url = http://controller:35357
auth_plugin = password
project_domain_id = default
user_domain_id = default
project_name = service
username = cinder
password = cinderpass

[oslo_concurrency]
lock_path = /var/lock/cinder

```

```ruby
#
su -s /bin/sh -c "cinder-manage db sync" cinder

service cinder-scheduler restart

service cinder-api restart

rm -f /var/lib/cinder/cinder.sqlite
```

#### 이제 Storage 노드를 설치한다.

#### Verify operation

```ruby
$ echo "export OS_VOLUME_API_VERSION=2" | tee -a admin-openrc.sh demo-openrc.sh
```

```ruby
$
source admin-openrc.sh

cinder service-list
```

```ruby
$
source demo-openrc.sh

cinder create --name demo-volume1 1

cinder list
```

## Object Storage(Swift)

#### To configure prerequisites
```ruby
$
source admin-openrc.sh

openstack user create --password-prompt swift
User Password:swiftpass
```
```ruby
$
openstack role add --project service --user swift admin

openstack service create --name swift --description "OpenStack Object Storage" object-store

openstack endpoint create \
  --publicurl 'http://controller:8080/v1/AUTH_%(tenant_id)s' \
  --internalurl 'http://controller:8080/v1/AUTH_%(tenant_id)s' \
  --adminurl http://controller:8080 \
  --region RegionOne \
  object-store
```

#### To install and configure the controller node components
```ruby
# apt-get install swift swift-proxy python-swiftclient python-keystoneclient python-keystonemiddleware memcached
```
```ruby
#
mkdir /etc/swift

curl -o /etc/swift/proxy-server.conf https://git.openstack.org/cgit/openstack/swift/plain/etc/proxy-server.conf-sample?h=stable/kilo
```
**/etc/swift/proxy-server.conf**

```
[DEFAULT]
bind_port = 8080
user = swift
swift_dir = /etc/swift

[pipeline:main]
pipeline = catch_errors gatekeeper healthcheck proxy-logging cache container_sync bulk ratelimit authtoken keystoneauth container-quotas account-quotas slo dlo proxy-logging proxy-server

[app:proxy-server]
account_autocreate = true

[filter:keystoneauth]
use = egg:swift#keystoneauth
operator_roles = admin,user

[filter:authtoken]
#Comment out or remove any other options in the [filter:authtoken] section.
paste.filter_factory = keystonemiddleware.auth_token:filter_factory
auth_uri = http://controller:5000
auth_url = http://controller:35357
auth_plugin = password
project_domain_id = default
user_domain_id = default
project_name = service
username = swift
password = swiftpass
delay_auth_decision = true

[filter:cache]
memcache_servers = 127.0.0.1:11211
```
#### 이제 각 스토리지 노드를 설치하고 온다.

### Account ring
```ruby
# cd /etc/swift
```

**account.builder (New File)**

```ruby
#
swift-ring-builder account.builder create 10 3 1
swift-ring-builder account.builder add r1z1-%STORAGE_NODE_MANAGEMENT_INTERFACE_IP_ADDRESS%:6002/DEVICE_NAME DEVICE_WEIGHT
예)
swift-ring-builder account.builder add r1z1-10.0.0.51:6002/sdb1 100
Device d0r1z1-10.0.0.51:6002R10.0.0.51:6002/sdb1_"" with 100.0 weight got id 0

swift-ring-builder account.builder add r1z2-10.0.0.51:6002/sdc1 100
Device d1r1z2-10.0.0.51:6002R10.0.0.51:6002/sdc1_"" with 100.0 weight got id 1

swift-ring-builder account.builder add r1z3-10.0.0.52:6002/sdb1 100
Device d2r1z3-10.0.0.52:6002R10.0.0.52:6002/sdb1_"" with 100.0 weight got id 2

swift-ring-builder account.builder add r1z4-10.0.0.52:6002/sdc1 100
Device d3r1z4-10.0.0.52:6002R10.0.0.52:6002/sdc1_"" with 100.0 weight got id 3
```

```ruby
#
swift-ring-builder account.builder
swift-ring-builder account.builder rebalance
```


### Container ring
```ruby
# cd /etc/swift
```
**container.builder (New File)**

```ruby
swift-ring-builder container.builder create 10 3 1
swift-ring-builder container.builder add r1z1-%STORAGE_NODE_MANAGEMENT_INTERFACE_IP_ADDRESS%:6001/DEVICE_NAME DEVICE_WEIGHT
예)
swift-ring-builder container.builder add r1z1-10.0.0.51:6001/sdb1 100
Device d0r1z1-10.0.0.51:6001R10.0.0.51:6001/sdb1_"" with 100.0 weight got id 0

swift-ring-builder container.builder add r1z2-10.0.0.51:6001/sdc1 100
Device d1r1z2-10.0.0.51:6001R10.0.0.51:6001/sdc1_"" with 100.0 weight got id 1

swift-ring-builder container.builder add r1z3-10.0.0.52:6001/sdb1 100
Device d2r1z3-10.0.0.52:6001R10.0.0.52:6001/sdb1_"" with 100.0 weight got id 2

swift-ring-builder container.builder add r1z4-10.0.0.52:6001/sdc1 100
Device d3r1z4-10.0.0.52:6001R10.0.0.52:6001/sdc1_"" with 100.0 weight got id 3
```

```ruby
#
swift-ring-builder container.builder
swift-ring-builder container.builder rebalance
```

### Object ring

```ruby
# cd /etc/swift
```

**container.builder (New File)**


```ruby
#
swift-ring-builder object.builder create 10 3 1
swift-ring-builder object.builder add r1z1-%STORAGE_NODE_MANAGEMENT_INTERFACE_IP_ADDRESS%:6000/DEVICE_NAME DEVICE_WEIGHT
예)
swift-ring-builder object.builder add r1z1-10.0.0.51:6000/sdb1 100
Device d0r1z1-10.0.0.51:6000R10.0.0.51:6000/sdb1_"" with 100.0 weight got id 0

swift-ring-builder object.builder add r1z2-10.0.0.51:6000/sdc1 100
Device d1r1z2-10.0.0.51:6000R10.0.0.51:6000/sdc1_"" with 100.0 weight got id 1

swift-ring-builder object.builder add r1z3-10.0.0.52:6000/sdb1 100
Device d2r1z3-10.0.0.52:6000R10.0.0.52:6000/sdb1_"" with 100.0 weight got id 2

swift-ring-builder object.builder add r1z4-10.0.0.52:6000/sdc1 100
Device d3r1z4-10.0.0.52:6000R10.0.0.52:6000/sdc1_"" with 100.0 weight got id 3
```

```ruby
#
swift-ring-builder object.builder
swift-ring-builder object.builder rebalance
```

### Distribute ring configuration files

**Copy the account.ring.gz, container.ring.gz, and object.ring.gz files to the /etc/swift directory on each storage node**


### Finalize installation

#### Configure hashes and default storage policy

```ruby
# curl -o /etc/swift/swift.conf https://git.openstack.org/cgit/openstack/swift/plain/etc/swift.conf-sample?h=stable/kilo
```
**/etc/swift/swift.conf**

```
[swift-hash]
swift_hash_path_suffix = HASH_PATH_SUFFIX
swift_hash_path_prefix = HASH_PATH_PREFIX

[storage-policy:0]
name = Policy-0
default = yes
```

**Copy the swift.conf file to the /etc/swift directory on each storage node**

**On all nodes**

```ruby
#
chown -R swift:swift /etc/swift
service memcached restart
service swift-proxy restart
```
**On the storage nodes**

```ruby
# swift-init all start
```

>The storage node runs many Object Storage services and the swift-init command makes them easier to manage. You can ignore errors from services not running on the storage node.

#### Verify operation
```ruby
$
source demo-openrc.sh
swift -V 3 stat
# Replace FILE with the name of a local file to upload to the demo-container1 container.
swift -V 3 upload demo-container1 FILE
```
```ruby
$ swift -V 3 list
swift -V 3 download demo-container1 FILE
```




## Orchestration module (Heat)

```ruby
$ mysql -u root -p
CREATE DATABASE heat;
GRANT ALL PRIVILEGES ON heat.* TO 'heat'@'localhost' IDENTIFIED BY 'heatdbpass';
GRANT ALL PRIVILEGES ON heat.* TO 'heat'@'%' IDENTIFIED BY 'heatdbpass';
```
```ruby
$
source admin-openrc.sh
openstack user create --password-prompt heat
User Password: heatpass
```
```ruby
$
openstack role add --project service --user heat admin

openstack role create heat_stack_owner

openstack role add --project demo --user demo heat_stack_owner

openstack role create heat_stack_user
```
>You must add the heat_stack_owner role to users that manage stacks.

```ruby
$
openstack service create --name heat --description "Orchestration" orchestration

openstack service create --name heat-cfn --description "Orchestration"  cloudformation

openstack endpoint create \
  --publicurl http://controller:8004/v1/%\(tenant_id\)s \
  --internalurl http://controller:8004/v1/%\(tenant_id\)s \
  --adminurl http://controller:8004/v1/%\(tenant_id\)s \
  --region RegionOne \
  orchestration

  openstack endpoint create \
    --publicurl http://controller:8000/v1 \
    --internalurl http://controller:8000/v1 \
    --adminurl http://controller:8000/v1 \
    --region RegionOne \
    cloudformation
```

#### To install and configure the Orchestration components

```ruby
# apt-get install heat-api heat-api-cfn heat-engine python-heatclient
```

**/etc/heat/heat.conf**

```
[DEFAULT]
verbose = True
rpc_backend = rabbit
heat_metadata_server_url = http://controller:8000
heat_waitcondition_server_url = http://controller:8000/v1/waitcondition
stack_domain_admin = heat_domain_admin
stack_domain_admin_password = heatdomainpass
stack_user_domain_name = heat_user_domain

[oslo_messaging_rabbit]
rabbit_host = controller
rabbit_userid = openstack
rabbit_password = rabbitpass

[database]
connection = mysql://heat:heatdbpass@controller/heat

[keystone_authtoken]
#Comment out any auth_host, auth_port, and auth_protocol
auth_uri = http://controller:5000/v2.0
identity_uri = http://controller:35357
admin_tenant_name = service
admin_user = heat
admin_password = heatpass

[ec2authtoken]
auth_uri = http://controller:5000/v2.0
```

```ruby
$
source admin-openrc.sh
heat-keystone-setup-domain \
  --stack-user-domain-name heat_user_domain \
  --stack-domain-admin heat_domain_admin \
  --stack-domain-admin-password heatdomainpass
```

```ruby
# su -s /bin/sh -c "heat-manage db_sync" heat
```

#### To finalize installation
```ruby
#
service heat-api restart
service heat-api-cfn restart
service heat-engine restart
rm -f /var/lib/heat/heat.sqlite
```
#### Verify operation
```ruby
$ source admin-openrc.sh
```
- Template Guide : http://docs.openstack.org/developer/heat/template_guide/index.html

**test-stack.yml**

```
heat_template_version: 2014-10-16
description: A simple server.

parameters:
  ImageID:
    type: string
    description: Image use to boot a server
  NetID:
    type: string
    description: Network ID for the server

resources:
  server:
    type: OS::Nova::Server
    properties:
      image: { get_param: ImageID }
      flavor: m1.tiny
      networks:
      - network: { get_param: NetID }

outputs:
  private_ip:
    description: IP address of the server in the private network
    value: { get_attr: [ server, first_address ] }
```
```ruby
$
NET_ID=$(nova net-list | awk '/ demo-net / { print $2 }')

heat stack-create -f test-stack.yml -P "ImageID=cirros-0.3.4-x86_64;NetID=$NET_ID" testStack

heat stack-list
```


## Telemetry Service (Ceilometer)

#### To configure prerequisites

```ruby
# apt-get install mongodb-server mongodb-clients python-pymongo
```

**/etc/mongodb.conf**

```
bind_ip = %MANAGEMENT_NODE_IP_ADDRESS%
smallfiles = true
```

```ruby
#
service mongodb stop
rm /var/lib/mongodb/journal/prealloc.*
service mongodb start
```

```ruby
# mongo --host controller --eval '
  db = db.getSiblingDB("ceilometer");
  db.addUser({user: "ceilometer",
  pwd: "ceilometerdbpass",
  roles: [ "readWrite", "dbAdmin" ]})'
```

```ruby
$
source admin-openrc.sh

openstack user create --password-prompt ceilometer
User Password: ceilometerpass
```
```ruby
$
openstack role add --project service --user ceilometer admin

openstack service create --name ceilometer --description "Telemetry" metering

openstack endpoint create \
  --publicurl http://controller:8777 \
  --internalurl http://controller:8777 \
  --adminurl http://controller:8777 \
  --region RegionOne \
  metering
```

#### To install and configure the Telemetry module components

```ruby
# apt-get install ceilometer-api ceilometer-collector ceilometer-agent-central ceilometer-agent-notification ceilometer-alarm-evaluator ceilometer-alarm-notifier python-ceilometerclient
```
**/etc/ceilometer/ceilometer.conf**

```
[DEFAULT]
verbose = True
rpc_backend = rabbit
auth_strategy = keystone

[oslo_messaging_rabbit]
rabbit_host = controller
rabbit_userid = openstack
rabbit_password = rabbitpass

[database]
connection = mongodb://ceilometer:ceilometerdbpass@controller:27017/ceilometer

[keystone_authtoken]
#Comment out any auth_host, auth_port, and auth_protocol
auth_uri = http://controller:5000/v2.0
identity_uri = http://controller:35357
admin_tenant_name = service
admin_user = ceilometer
admin_password = ceilometerpass

[service_credentials]
os_auth_url = http://controller:5000/v2.0
os_username = ceilometer
os_tenant_name = service
os_password = ceilometerpass
os_endpoint_type = internalURL
os_region_name = RegionOne

[publisher]
telemetry_secret = 969c6c4413e693901809
```
#### To finalize installation

```ruby
#
service ceilometer-agent-central restart
service ceilometer-agent-notification restart
service ceilometer-api restart
service ceilometer-collector restart
service ceilometer-alarm-evaluator restart
service ceilometer-alarm-notifier restart
```

#### Configure the Image service

**/etc/glance/glance-api.conf**
**/etc/glance/glance-registry.conf**

```
[DEFAULT]
notification_driver = messagingv2
rpc_backend = rabbit
rabbit_host = controller
rabbit_userid = openstack
rabbit_password = rabbitpass
```

```ruby
#
service glance-registry restart
service glance-api restart
```

#### Configure the Block Storage service

**/etc/cinder/cinder.conf**

```
[DEFAULT]
control_exchange = cinder
notification_driver = messagingv2
```

```ruby
#
service cinder-api restart
service cinder-scheduler restart
```

#### 이제 컴퓨트노드와 스토리지 노드도 설정한다.

> cinder-volume-usage-audit command : http://docs.openstack.org/admin-guide-cloud/telemetry-data-collection.html#block-storage-audit-script-setup-to-get-notifications


#### Verify the Telemetry installation

```ruby
$
source admin-openrc.sh

ceilometer meter-list
```
만약 meter-list에 아무것도 출력되지 않는다면, 아래 명령을 실행한다.

```ruby
$
unset OS_PROJECT_DOMAIN_ID
unset OS_USER_DOMAIN_ID
export OS_AUTH_URL=http://controller:35357/v2.0
```

또는 ceilometer-admin-openrc.sh 을 아래와 같이 생성한다.

**ceilometer-admin-openrc.sh**

```
unset OS_PROJECT_DOMAIN_ID
unset OS_USER_DOMAIN_ID

export OS_PROJECT_NAME=admin
export OS_TENANT_NAME=admin
export OS_USERNAME=admin
export OS_PASSWORD=adminpass #ADMIN_PASS
export OS_AUTH_URL=http://controller:35357/v2.0

export OS_IMAGE_API_VERSION=2
export OS_VOLUME_API_VERSION=2
```


```ruby
$
IMAGE_ID=$(glance image-list | grep 'cirros-0.3.4-x86_64' | awk '{ print $2 }')

glance image-download $IMAGE_ID > /tmp/cirros.img

ceilometer meter-list

ceilometer statistics -m image.download -p 60
```

```ruby
$ rm /tmp/cirros.img
```



