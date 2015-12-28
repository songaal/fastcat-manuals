# 오픈스택 오브젝트 스토리지 노드

#### 네트워크 설정

**/etc/network/interfaces**

```
# Management
auto eth0
iface eth0 inet static
address 10.0.1.51
netmask 255.255.255.0
gateway 10.0.1.1

# Storage
auto eth1
iface eth1 inet static
address 10.0.3.51
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

# object1
10.0.1.51       object1
```

#### Verify connectivity

```ruby
ping -c 4 openstack.org
ping -c 4 network
ping -c 4 compute1
ping -c 4 controller
ping -c 4 block1
ping -c 4 object1
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

## Another packages
```ruby
# apt-get install xfsprogs rsync
```
```ruby
#
mkfs.xfs /dev/sdb1
mkfs.xfs /dev/sdc1
mkdir -p /srv/node/sdb1
mkdir -p /srv/node/sdc1
```
**/etc/fstab**

```
/dev/sdb1 /srv/node/sdb1 xfs noatime,nodiratime,nobarrier,logbufs=8 0 2
/dev/sdc1 /srv/node/sdc1 xfs noatime,nodiratime,nobarrier,logbufs=8 0 2
```

```ruby
#
mount /srv/node/sdb1
mount /srv/node/sdc1
```

**/etc/rsyncd.conf**

```
uid = swift
gid = swift
log file = /var/log/rsyncd.log
pid file = /var/run/rsyncd.pid
address = %MANAGEMENT_INTERFACE_IP_ADDRESS%

[account]
max connections = 2
path = /srv/node/
read only = false
lock file = /var/lock/account.lock

[container]
max connections = 2
path = /srv/node/
read only = false
lock file = /var/lock/container.lock

[object]
max connections = 2
path = /srv/node/
read only = false
lock file = /var/lock/object.lock
```

**/etc/default/rsync**

```
RSYNC_ENABLE=true
```

```ruby
# service rsync start
```

## OpenStack packages

#### To enable the OpenStack repository

```ruby
#
apt-get install ubuntu-cloud-keyring

echo "deb http://ubuntu-cloud.archive.canonical.com/ubuntu" "trusty-updates/kilo main" > /etc/apt/sources.list.d/cloudarchive-kilo.list
```

#### To finalize installation

```ruby
# apt-get update && apt-get dist-upgrade
```

#### Install and configure storage node components

```ruby
# apt-get install swift swift-account swift-container swift-object
```
```ruby
#
curl -o /etc/swift/account-server.conf https://git.openstack.org/cgit/openstack/swift/plain/etc/account-server.conf-sample?h=stable/kilo

curl -o /etc/swift/container-server.conf https://git.openstack.org/cgit/openstack/swift/plain/etc/container-server.conf-sample?h=stable/kilo

curl -o /etc/swift/object-server.conf https://git.openstack.org/cgit/openstack/swift/plain/etc/object-server.conf-sample?h=stable/kilo

curl -o /etc/swift/container-reconciler.conf https://git.openstack.org/cgit/openstack/swift/plain/etc/container-reconciler.conf-sample?h=stable/kilo

curl -o /etc/swift/object-expirer.conf https://git.openstack.org/cgit/openstack/swift/plain/etc/object-expirer.conf-sample?h=stable/kilo
```

**/etc/swift/account-server.conf**

```
[DEFAULT]
bind_ip = %MANAGEMENT_INTERFACE_IP_ADDRESS%
bind_port = 6002
user = swift
swift_dir = /etc/swift
devices = /srv/node

[pipeline:main]
pipeline = healthcheck recon account-server

[filter:recon]
recon_cache_path = /var/cache/swift
```

**/etc/swift/container-server.conf**

```
[DEFAULT]
bind_ip = %MANAGEMENT_INTERFACE_IP_ADDRESS%
bind_port = 6001
user = swift
swift_dir = /etc/swift
devices = /srv/node

[pipeline:main]
pipeline = healthcheck recon container-server

[filter:recon]
recon_cache_path = /var/cache/swift
```

**/etc/swift/object-server.conf**

```
[DEFAULT]
bind_ip = %MANAGEMENT_INTERFACE_IP_ADDRESS%
bind_port = 6000
user = swift
swift_dir = /etc/swift
devices = /srv/node

[pipeline:main]
pipeline = healthcheck recon object-server

[filter:recon]
recon_cache_path = /var/cache/swift
recon_lock_path = /var/lock
```

```ruby
#
chown -R swift:swift /srv/node
mkdir -p /var/cache/swift
chown -R swift:swift /var/cache/swift
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



