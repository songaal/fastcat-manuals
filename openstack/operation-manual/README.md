# 오픈스택 운영매뉴얼



## 프로젝트 및 사용자 추가

최초 설치시, 프로젝트는 admin과 demo가 존재한다.

admin 프로젝트는 admin계정이 사용하는 관리용도이므로, 그대로 두지만, demo프로젝트는 데모용도이므로, 삭제해도 무방하다.

신규로 `team1` 이라는 프로젝트를 만들고, `manager1`이라는 계정으로 관리하고자 한다면, 아래와 같이 수행한다.

```ruby
$ source admin-openrc.sh
```

```ruby
$ openstack project create --description "Team1 Project" team1
$ openstack user create --password-prompt manager1
패스워드 :
```
```ruby
$ openstack role add --project team1 --user manager1 user
```

이제 Horizon관리도구를 통해 로그인하여 프로젝트를 관리할 수 있다.

만약, 관리도구가 아닌, Command 를 통해 openstack 명령어를 수행하고자 하면, 아래와 같이 스크립트를 만든다.

**team1-openrc.sh**

```
export OS_PROJECT_DOMAIN_ID=defaultexport OS_USER_DOMAIN_ID=defaultexport OS_PROJECT_NAME=team1export OS_TENANT_NAME=team1export OS_USERNAME=manager1export OS_PASSWORD=%PASSWORD% 
export OS_AUTH_URL=http://controller:5000/v3
```

사용하려면, `source`로 읽은뒤, 명령을 수행한다.

```ruby
$ source team1-openrc.sh
$ neutron net-list
+--------------------------------------+----------+-----------------------------------------------------+
| id                                   | name     | subnets                                             |
+--------------------------------------+----------+-----------------------------------------------------+
| 72740fee-6c99-42c5-a15a-5a3b983d8c00 | demo-net | 3427dbaf-73e2-4587-ab06-fcfccdc46a88 192.168.1.0/24 |
| a2024656-9bfc-49c4-878e-7f89a3319cda | team1-net  | fa473ad6-807e-44c1-ad68-a50e74667ef8                |
+--------------------------------------+----------+-----------------------------------------------------+
```


## 컴퓨트 노드 증설

인스턴스의 사용량이 늘어나 추가적으로 컴퓨트 노드가 필요할 때 수행하며, 기본적으로 설치한다.

컴퓨트 노드는 Management 네트워크와 Storage 네트워크를 사용하므로, 이에 따른 이더넷카드연결을 먼저 수행한다.

각 노드의 /etc/hosts 파일을 아래를 참고하여 설정한다.

예) compute7을 추가할때

```
# compute7
10.0.1.37       compute7
```

그외에 컨트롤러 노드에서 설정해줄 것은 없으며, 새 컴퓨트 노드에  `컴퓨트 노드 설치매뉴얼`을 따라서 NTP, Nova, Neutron을 설치한뒤, 컨트롤러 노드에서 아래 명령으로 추가된 상태를 확인한다.

```ruby
$ source admin-openrc.sh
$ nova service-list
```


## 볼륨연동후 사용설정

오픈스택에서 볼륨을 생성후 인스턴스에 연동을 하면, 실제로는 바로 사용할 수 없으며 파티션을 생성 및 마운트작업이 추가로 필요하다.

먼저 인스턴스와 볼륨의 연동이 완료되면, 인스턴스에 `ssh` 로 접속하여 아래와 같이 정보를 확인할 수 있다.

```ruby
# fdisk -l
Disk /dev/vda: 42.9 GB, 42949672960 bytes
4 heads, 32 sectors/track, 655360 cylinders, total 83886080 sectors
Units = sectors of 1 * 512 = 512 bytes
Sector size (logical/physical): 512 bytes / 512 bytes
I/O size (minimum/optimal): 512 bytes / 512 bytes
Disk identifier: 0x00037603

   Device Boot      Start         End      Blocks   Id  System
/dev/vda1   *        2048    83886079    41942016   83  Linux

Disk /dev/vdb: 5368 MB, 5368709120 bytes
16 heads, 63 sectors/track, 10402 cylinders, total 10485760 sectors
Units = sectors of 1 * 512 = 512 bytes
Sector size (logical/physical): 512 bytes / 512 bytes
I/O size (minimum/optimal): 512 bytes / 512 bytes
Disk identifier: 0x00000000

Disk /dev/vdb doesn't contain a valid partition table
```

새로 추가된 디스크가 `/dev/vdb`로 인식되었으므로 파티션을 생성하도록 한다.

```ruby
# fdisk /dev/vdb
Device contains neither a valid DOS partition table, nor Sun, SGI or OSF disklabel
Building a new DOS disklabel with disk identifier 0x10c4edf8.
Changes will remain in memory only, until you decide to write them.
After that, of course, the previous content won't be recoverable.

Warning: invalid flag 0x0000 of partition table 4 will be corrected by w(rite)

Command (m for help):
```

신규로 만들것이므로 `n` 을 눌러서 진행한다.
선택옵션은 아래를 참조한다.
마지막에는 `w`를 입력해서 반드시 저장하고 빠져나오도록 한다.

```ruby
Command (m for help): n
Partition type:
   p   primary (0 primary, 0 extended, 4 free)
   e   extended
Select (default p): p
Partition number (1-4, default 1): 1
First sector (2048-10485759, default 2048):
Using default value 2048
Last sector, +sectors or +size{K,M,G} (2048-10485759, default 10485759):
Using default value 10485759

Command (m for help):w
```

이제 `fdisk`로 확인해보면 새로 생성된 `/dev/vdb1`을 확인할 수 있다.

```ruby
# fdisk -l

Disk /dev/vda: 42.9 GB, 42949672960 bytes
4 heads, 32 sectors/track, 655360 cylinders, total 83886080 sectors
Units = sectors of 1 * 512 = 512 bytes
Sector size (logical/physical): 512 bytes / 512 bytes
I/O size (minimum/optimal): 512 bytes / 512 bytes
Disk identifier: 0x00037603

   Device Boot      Start         End      Blocks   Id  System
/dev/vda1   *        2048    83886079    41942016   83  Linux

Disk /dev/vdb: 5368 MB, 5368709120 bytes
9 heads, 40 sectors/track, 29127 cylinders, total 10485760 sectors
Units = sectors of 1 * 512 = 512 bytes
Sector size (logical/physical): 512 bytes / 512 bytes
I/O size (minimum/optimal): 512 bytes / 512 bytes
Disk identifier: 0xcbb27ca7

   Device Boot      Start         End      Blocks   Id  System
/dev/vdb1            2048    10485759     5241856   83  Linux
```

파일 시스템을 EXT4로 포맷하기 위해 `mkfs.ext4` 명령어를 이용한다.
타 파일시스템은 아래 명령어를 참고한다.
 * ext2 : mkfs.ext2
 * ext3 : mkfs.ext3
 * ext4 : mkfs.ext4
 * ntfs : mkfs.ntfs
 * vfat(fast32) : mkfs.vfat -F 32

```ruby
# mkfs.ext4 /dev/vdb1
mke2fs 1.42.9 (4-Feb-2014)
Filesystem label=
OS type: Linux
Block size=4096 (log=2)
Fragment size=4096 (log=2)
Stride=0 blocks, Stripe width=0 blocks
327680 inodes, 1310464 blocks
65523 blocks (5.00%) reserved for the super user
First data block=0
Maximum filesystem blocks=1342177280
40 block groups
32768 blocks per group, 32768 fragments per group
8192 inodes per group
Superblock backups stored on blocks:
	32768, 98304, 163840, 229376, 294912, 819200, 884736

Allocating group tables: done
Writing inode tables: done
Creating journal (32768 blocks): done
Writing superblocks and filesystem accounting information: done
```

이제 디스크를 특정위치에 마운트할 차례이다.
먼저 특정위치의 디렉토리를 만들고 `mount` 명령어를 사용해서 연결한다.

```ruby
# mkdir disk2
# mount /dev/vdb1 disk2
```

`df` 명령어로 확인하면 `/dev/vdb1` 가 `disk2` 에 연결된 것을 확인할 수 있다.

```ruby
# df -T
Filesystem     Type     1K-blocks   Used Available Use% Mounted on
/dev/vda1      ext4      41251136 797748  38744948   3% /
none           tmpfs            4      0         4   0% /sys/fs/cgroup
udev           devtmpfs   2019120     12   2019108   1% /dev
tmpfs          tmpfs       404816    340    404476   1% /run
none           tmpfs         5120      0      5120   0% /run/lock
none           tmpfs      2024072      0   2024072   0% /run/shm
none           tmpfs       102400      0    102400   0% /run/user
/dev/vdb1      ext4       5028480  10232   4739772   1% /home/ubuntu/disk2
```

여기까지 하면 재부팅시 마운트 정보가 사라지므로 fstab 파일에 기록을 하도록 한다.
**/etc/fstab**
```
/dev/vdb1 /home/ubuntu/disk2  ext4  defaults  0  0
```
