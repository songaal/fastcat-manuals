# 오픈스택 컴퓨트 노드

#### 네트워크 설정

**/etc/network/interfaces**

```
# Management
auto eth0
iface eth0 inet static
address 10.0.1.31
netmask 255.255.255.0
gateway 10.0.1.1

# Tunnel
auto eth1
iface eth1 inet static
address 10.0.2.31
netmask 255.255.255.0

# Storage
auto eth1
iface eth1 inet static
address 10.0.3.31
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

#### 한번에 모두 설치
```ruby
apt-get install -y nova-compute sysfsutils neutron-plugin-ml2 neutron-plugin-openvswitch-agent
```


## Compute service

### Install and configure a compute node
#### To install and configure the Compute hypervisor components

```ruby
apt-get install nova-compute sysfsutils
```

**/etc/nova/nova.conf**

```
[DEFAULT]
rpc_backend = rabbit
auth_strategy = keystone
my_ip = 10.0.0.31 #Compute node MANAGEMENT_IP_ADDRESS
vnc_enabled = True
vncserver_listen = 0.0.0.0
vncserver_proxyclient_address = MANAGEMENT_INTERFACE_IP_ADDRESS
novncproxy_base_url = http://#CONTROLLER_NODE_MANAGEMENT_IP_ADDRESS:6080/vnc_auto.html
verbose = True

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
username = nova
password = novapass

[glance]
host = controller

[oslo_concurrency]
lock_path = /var/lib/nova/tmp
```

#### To finalize installation
Determine whether your compute node supports hardware acceleration for virtual machines:
```ruby
$ egrep -c '(vmx|svm)' /proc/cpuinfo
```

> 리턴값이 0이면 하드웨어 가속을 지원하지 않으므로, QEMU를 사용.
리턴값이 1이상이면, KVM사용가능.

**/etc/nova/nova-compute.conf**
```
[libvirt]
virt_type = kvm
```

```ruby
service nova-compute restart

rm -f /var/lib/nova/nova.sqlite
```

## OpenStack Networking (neutron)

### Install and configure compute node

**/etc/sysctl.conf**

```
net.ipv4.conf.all.rp_filter=0
net.ipv4.conf.default.rp_filter=0
net.bridge.bridge-nf-call-iptables=1
net.bridge.bridge-nf-call-ip6tables=1
```
```ruby
sysctl -p
```

```ruby
apt-get install neutron-plugin-ml2 neutron-plugin-openvswitch-agent
```

#### To configure the Networking common components (네트워크노드와 거의 동일하다)

**/etc/neutron/neutron.conf**

```
[database]
# comment any connection options

[DEFAULT]
verbose = True
rpc_backend = rabbit
auth_strategy = keystone
core_plugin = ml2
service_plugins = router
allow_overlapping_ips = True

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
username = neutron
password = neutronpass
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

[ovs]
local_ip = MY_INSTANCE_TUNNELS_INTERFACE_IP_ADDRESS

[agent]
tunnel_types = gre
```

#### To configure the Open vSwitch (OVS) service

```ruby
service openvswitch-switch restart
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

#### To finalize the installation

```ruby
service nova-compute restart

service neutron-plugin-openvswitch-agent restart
```

## Telemetry Service (Ceilometer)

```ruby
# apt-get install ceilometer-agent-compute
```

**/etc/ceilometer/ceilometer.conf**

```
[DEFAULT]
verbose = True
rpc_backend = rabbit

[oslo_messaging_rabbit]
rabbit_host = controller
rabbit_userid = openstack
rabbit_password = rabbitpass

[publisher]
telemetry_secret = 969c6c4413e693901809

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

```

**/etc/nova/nova.conf**

```
[DEFAULT]
instance_usage_audit = True
instance_usage_audit_period = hour
notify_on_state_change = vm_and_task_state
notification_driver = messagingv2
```

```ruby
#
service ceilometer-agent-compute restart
service nova-compute restart
```

















