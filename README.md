# General

MPEPerms by XackiGiFF is a permissions manager for PocketMine-MP.

It can be used in conjunction with MPEChat to display players groups in chat.

**NOTE: This is unofficial version plugin, forked from PurePemrs **

May be it's time for everyone to use alternatives as PurePerms & PureChat is outdated in a sense of the features itself and the functionality. The official alternatives that are actually better than PurePerms and PureChat are the following: [GroupsAPI](https://github.com/alvin0319/GroupsAPI) & [RankSystem](https://github.com/IvanCraft623/RankSystem)

But... I never gonna give you up, my favorite plugin for permissions manager.

# Commands

All stay as is

Command | Description | Permission
--- | --- | ---
`/addgroup <group>` | Adds a new group to the groups list. | mpeperms.command.addgroup
`/addparent <target_group> <parent_group>` | Adds a group to another group inheritance list. | mpeperms.command.addparent
`/defgroup <group> [world]` | Allows you to set default group. | mpeperms.command.defgroup
`/fperms` | Allows you to find permissions for a specific plugin. | mpeperms.command.fperms
`/groups` | Shows a list of all groups. | mpeperms.command.groups
`/grpinfo <group> [world]` | Shows info about a group. | mpeperms.command.grpinfo
`/listgperms <group> <page> [world]` | Shows a list of all permissions from a group. | mpeperms.command.listgperms
`/listuperms <player> <page> [world]` | Shows a list of all permissions from a user. | mpeperms.command.listuperms
`/ppinfo` | Shows info about MPErePerms. | mpeperms.command.ppinfo
`/ppsudo <login / register>` | Registers or logs into your Noeul account. | mpeperms.command.ppsudo
`/ppreload` | Reloads all MPErePerms configurations. | mpeperms.command.ppreload
`/rmgroup <group>` | Removes a group from the groups list. | mpeperms.command.rmgroup
`/rmparent <target_group> <parent_group>` | Removes a group from another group inheritance list. | mpeperms.command.rmparent
`/setgperm <group> <permission> [world]` | Adds a permission to the group. | mpeperms.command.setgperm
`/setgroup <player> <group> [world]` | Sets group for the user. | mpeperms.command.setgroup
`/setuperm <player> <permission> [world]` | Adds a permission to the user. | mpeperms.command.setuperm
`/unsetgperm <group> <permission> [world]` | Removes a permission from the group. | mpeperms.command.unsetgperm
`/unsetuperm <player> <permission> [world]` | Removes a permission from the user. | mpeperms.command.unsetuperm
`/usrinfo <player> [world]` | Shows info about a user. | mpeperms.command.usrinfo

# Config

``` YAML

# MPErePerms by 64FF00 (xktiverz@gmail.com, @64ff00 for Twitter)

# 제 블로그 이외에 허락없이 마음대로 플러그인 배포하실 시에는 바로 한국어 파일 삭제 조치하고 공유 중단합니다

---
# Set default data provider for MPErePerms
# - mysql, yamlv1, yamlv2
data-provider: yamlv1

# Set the default language for MPErePerms (<3)
# - en, ko, jp, ru, ua, it, sp, cz, sk, de, idn, tr
# English message resource by @64FF00 and @Samueljh1 (GitHub)
# Korean message resource by @64FF00 (GitHub)
# Japanese message resource by @onebone and @haniokasai (GitHub)
# Russian message resource by @vvzar and @Pub4Game (GitHub)
# Ukrainian message resource by @samalero (GitHub)
# Italian message resource by @AryToNeX (GitHub)
# Spanish message resource by @iksaku and @JoahDave (Github) 
# Czech message resource by @Michael2010117 (GitHub)
# Slovak message resource by @Michael2010117 (GitHub)
# German message resource by @Exxarion (GitHub)
# Indonesian message resource by @DevillordMCPE (GitHub)
# Turkish messages resource by @PainTR (GitHub)
default-language: en

# Disable /op permission for all players
# - true / false
disable-op: true

# Setting this option will allow you to use per-world permissions
# - true / false
enable-multiworld-perms: false

# Enables 'Noeul', a 'pointless' security management system for MPErePerms
# - true / false
enable-noeul-sixtyfour: false

# MySQL Settings (Only configure this if you are going to use MySQL data provider)
mysql-settings:
  host: "MPErePerms-FTW.loveyou.all"
  port: 3306
  user: "YourUsernameGoesHere"
  password: "YourPasswordGoesHere"
  db: "YourDBNameGoesHere"
  
# Sets a minimum length for a Noeul password when registering a new account
# - int  
noeul-minimum-pw-length: 6
  
# Special thanks to @jazzwhistle for helping me with this cool feature! #JAZZWHISTLE-FTW
# Ranks that can only be set on console
# Also, users with a superadmin-rank can only have their rank changed on console
# - array
superadmin-ranks: ["OP"]
```

```
YAML
---
Guest:
  alias: 'gst'
  isDefault: true
  inheritance: []
  permissions:
  - -essentials.kit
  - -essentials.kit.other
  - -pocketmine.command.me
  - pchat.colored.format
  - pchat.colored.nametag
  - pocketmine.command.list
  - mpeperms.command.ppinfo
  worlds: []
```

# Features

- Set up permissions for different groups!
- Multi-group inheritance system to allow you to inherit group permissions
- Multi-language support, just choose your favorite language in config.yml! (Currently supports Czech, English, German,
  Korean, Japanese, Russian, Italian, Indonesian, Slovak, Spanish, Turkish, and Ukrainian! :D)
- Supports YAML + MySQL providers
- Provides simple and flexible MPErePerms API for plugin developers
- And so on... ;)

