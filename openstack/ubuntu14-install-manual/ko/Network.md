# 오픈스택 네트워크 노드

#### 네트워크 설정

**/etc/network/interfaces**

```
# Management
auto eth0
iface eth0 inet static
address 10.0.1.21
netmask 255.255.255.0
gateway 10.0.1.1

# Tunnel
auto eth1
iface eth1 inet static
address 10.0.2.21
netmask 255.255.255.0

# External
auto INTERFACE_NAME
iface INTERFACE_NAME inet manual
up ip link set dev $IFACE up
down ip link set dev $IFACE down
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
apt-get install -y neutron-plugin-ml2 neutron-plugin-openvswitch-agent neutron-l3-agent neutron-dhcp-agent neutron-metadata-agent
```



## OpenStack Networking (neutron)

###Install and configure network node
####To configure prerequisites

**/etc/sysctl.conf**

```
net.ipv4.ip_forward=1
net.ipv4.conf.all.rp_filter=0
net.ipv4.conf.default.rp_filter=0
```
```ruby
sysctl -p
```
#### To install the Networking components
```ruby
apt-get install neutron-plugin-ml2 neutron-plugin-openvswitch-agent neutron-l3-agent neutron-dhcp-agent neutron-metadata-agent
```

#### To configure the Networking common components

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

[ml2_type_flat]
# 외부 네트워크가 여러개면 그 이름을 컴마단위로 나열한다. ex)ex1,ex2,ex3
flat_networks = external

[ml2_type_gre]
tunnel_id_ranges = 1:1000

[securitygroup]
enable_security_group = True
enable_ipset = True
firewall_driver = neutron.agent.linux.iptables_firewall.OVSHybridIptablesFirewallDriver

[ovs]
local_ip = %MY_TUNNELS_INTERFACE_IP_ADDRESS%
# 외부 네트워크가 여러개면 그 매핑을 컴마단위로 나열한다. ex) ext2:br-ex2,ext3:br-ex3
bridge_mappings = external:br-ex

[agent]
tunnel_types = gre
```

**/etc/neutron/l3_agent.ini**

```
[DEFAULT]
verbose = True
interface_driver = neutron.agent.linux.interface.OVSInterfaceDriver
external_network_bridge =
router_delete_namespaces = True
```


**/etc/neutron/dhcp_agent.ini**

```
[DEFAULT]
verbose = True
interface_driver = neutron.agent.linux.interface.OVSInterfaceDriver
dhcp_driver = neutron.agent.linux.dhcp.Dnsmasq
dhcp_delete_namespaces = True
dnsmasq_config_file = /etc/neutron/dnsmasq-neutron.conf
```

**/etc/neutron/dnsmasq-neutron.conf** (Create)

```
dhcp-option-force=26,1454
```

```ruby
pkill dnsmasq
```

#### To configure the metadata agent

**/etc/neutron/metadata_agent.ini**

```
[DEFAULT]
auth_uri = http://controller:5000
auth_url = http://controller:35357
auth_region = RegionOne
auth_plugin = password
project_domain_id = default
user_domain_id = default
project_name = service
username = neutron
password = neutronpass

nova_metadata_ip = controller
metadata_proxy_shared_secret = c8c6fd0b91a39cf2024e

verbose = True
```

#### 이제 여기서 controller 노드로 넘어가서 “Network 노드를 설정한뒤에 해야할일"을 수행하고 온다.

#### To configure the Open vSwitch (OVS) service

* 위의 `ml2_conf.ini`에서 만든 `bridge_mappings`의 브릿지를 실제로 생성해준다. ex) br-ex,ex1,ex2..
* `EXTERNAL_NETWORK_INTERFACE_NAME` 은 외부연결용 이더넷카드 이름이다. /etc/network/interfaces 에서 설정한 이름을 사용한다. ex) eth0, eth1, p4p1

```ruby
service openvswitch-switch restart

ovs-vsctl add-br br-ex

ovs-vsctl add-port br-ex EXTERNAL_NETWORK_INTERFACE_NAME
```

```ruby
service neutron-plugin-openvswitch-agent restart
service neutron-l3-agent restart
service neutron-dhcp-agent restart
service neutron-metadata-agent restart
```

#### Verify operation
이 부분은 controller 노드에서 수행해야 한다.