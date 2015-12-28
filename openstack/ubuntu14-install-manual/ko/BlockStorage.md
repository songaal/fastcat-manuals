# 오픈스택 블록스토리지 노드

#### 네트워크 설정

**/etc/network/interfaces**

```
# Management
auto eth0
iface eth0 inet static
address 10.0.1.41
netmask 255.255.255.0
gateway 10.0.1.1

# Storage
auto eth1
iface eth1 inet static
address 10.0.3.41
netmask 255.255.255.0
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
server controller iburst
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

## Block Storage (Cinder)

```ruby
#
apt-get install qemu

apt-get install lvm2

pvcreate /dev/sdb1
```


>If your system uses a different device name, adjust these steps accordingly.
>```ruby
vgcreate cinder-volumes /dev/sdb1
```

**/etc/lvm/lvm.conf**

```
devices {

filter = [ "a/sdb/", "r/.*/"]
```

#### Install and configure Block Storage volume components
```ruby
# apt-get install cinder-volume python-mysqldb
```

**/etc/cinder/cinder.conf**

```
[DEFAULT]
rpc_backend = rabbit
auth_strategy = keystone
my_ip = MY_MANAGEMENT_INTERFACE_IP_ADDRESS
enabled_backends = lvm
glance_host = controller
verbose = True

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

[lvm]
volume_driver = cinder.volume.drivers.lvm.LVMVolumeDriver
volume_group = cinder-volumes
iscsi_protocol = iscsi
iscsi_helper = tgtadm

[oslo_concurrency]
lock_path = /var/lock/cinder
```

```ruby
#
service tgt restart

service cinder-volume restart

rm -f /var/lib/cinder/cinder.sqlite
```

## Telemetry Service (Ceilometer)

#### Configure the Block Storage service

**/etc/cinder/cinder.conf**

```
[DEFAULT]
control_exchange = cinder
notification_driver = messagingv2
```

```ruby
# service cinder-volume restart
```


