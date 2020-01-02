# Blocking all Youtube video Advertisements
Youtube Video ADs blocker for DDWRT with Unbound DNS

Hi Everyone,

I was working on blocking all Youtube video ADs. I guess I found a solution and I would like to share with DDWRT community. For my tests my solution perfectly blocks all video ads on Youtube doesn't matter Mobile, Android TV or Web Browser.

Youtube pushes ADs thru thousands of hostnames. I blocked all of them via Unbound DNS. Official Youtube app shows yellow AD fractions on timeline of the video but video will never display. Here is the step by step tutorial.

## Requirments:

* DDWRT router with Unbound (Recursive DNS) feature (You can check SETUP screen. Make sure you will see and checked "Recursive DNS Resolving (Unbound)" feature on DDWRT). I use DD-WRT v3.0-r38060 std 12/20/18 firmware and WRT1900AC v1 router.
* PHP Hosting

There are several Youtube ADs hostnames lists on the internet. My code merges two of them. One dynamic (https://api.hackertarget.com/hostsearch/?q=googlevideo.com) other one static list. By the way JFFS is my external drive:
/tmp/mnt/sda1/Backups/jffs/unbound/named.cache


## STEP ONE: PREPARE PHP FILE FOR UNBOUND DNS

You can download my PHP file via github (It is too big to share here). This PHP file prepares a rules to block youtube ADs for Unbound DNS. Output of my PHP file like that:

local-data: "r1---sn-25ge7n76.googlevideo.com A 127.0.0.1"
local-data: "r1.sn-25ge7n76.googlevideo.com A 127.0.0.1"
local-data: "r2---sn-25ge7n76.googlevideo.com A 127.0.0.1"
local-data: "r2.sn-25ge7n76.googlevideo.com A 127.0.0.1"
local-data: "r3---sn-25ge7n76.googlevideo.com A 127.0.0.1"
local-data: "r3.sn-25ge7n76.googlevideo.com A 127.0.0.1"
local-data: "r4---sn-25ge7n76.googlevideo.com A 127.0.0.1"
local-data: "r4.sn-25ge7n76.googlevideo.com A 127.0.0.1"
local-data: "r5---sn-25ge7n76.googlevideo.com A 127.0.0.1"

## STEP TWO: ADD STARTUP LINES

sleep 5
stopservice unbound -f
unbound -c /tmp/mnt/sda1/Backups/jffs/unbound/unbound.conf

Above code stops Unbound service and starts with new configuration. You can change config file location accordingly. 

## STEP THREE: ADD FOLLOWING UNBOUND CONFIG FILE TO YOUR EXTERNAL DRIVE JFFS OR SOMEWHERE IN YOUR SYSTEM

Your local hosts and settings should be different than mine. So the best way just copy the original /tmp/unbound.conf file and add lines that I indicated below:

server:
verbosity: 1
interface: 0.0.0.0
interface: ::0
outgoing-num-tcp: 10
incoming-num-tcp: 10
msg-buffer-size: 8192
msg-cache-size: 1m
num-queries-per-thread: 30
rrset-cache-size: 2m
infra-cache-numhosts: 200
username: ""
pidfile: "/var/run/unbound.pid"
root-hints: "/tmp/mnt/sda1/Backups/jffs/unbound/named.cache" #Download updated named.cache and relocate accordingly your system Download from here https://www.internic.net/domain/named.root
target-fetch-policy: "2 1 0 0 0 0"
harden-short-bufsize: yes
harden-large-queries: yes
auto-trust-anchor-file: "/etc/unbound/root.key"
key-cache-size: 100k
neg-cache-size: 10k
num-threads: 2
so-reuseport: yes
msg-cache-slabs: 2
rrset-cache-slabs: 2
infra-cache-slabs: 2
key-cache-slabs: 2
outgoing-range: 462
access-control: 127.0.0.0/8 allow
access-control: 192.168.20.1/24 allow
access-control: 192.168.21.1/24 allow
local-data: "localhost A 127.0.0.1"
local-data: "WRT1900AC A 192.168.20.1"
local-data: "WRT1900AC.local A 192.168.20.1"
local-data: "VPN-SERVER.local A 192.168.20.2"
local-data: "WDMYCLOUD.local A 192.168.20.3"
local-data: "ASUS-N550JK.local A 192.168.20.100"
local-data: "COMPANY.local A 192.168.20.101"
local-data: "PLAYSTATION-3.local A 192.168.20.104"
local-data: "CANON-MG6450.local A 192.168.20.105"
local-data: "SOFTETHER_VPN.local A 192.168.20.106"
local-data: "XIAOMI-MIBOX.local A 192.168.20.107"
local-data: "EP-3703.local A 192.168.20.108"
local-data: "LGWEBOSTV.local A 192.168.20.109"

# ADD FOLLOWING LINES TO YOUR ORIGINAL UNBOUND.CONF FILE

# Blocking Ad Server domains. Google's AdSense, DoubleClick and Yahoo
# account for a 70 percent share of all advertising traffic. Block them.
local-zone: "doubleclick.net" redirect
local-data: "doubleclick.net A 127.0.0.1"
local-zone: "ads.youtube.com" redirect
local-data: "ads.youtube.com A 127.0.0.1"
local-zone: "adserver.yahoo.com" redirect
local-data: "adserver.yahoo.com A 127.0.0.1"

local-zone: "manifest.googlevideo.com" redirect
local-data: "manifest.googlevideo.com A 172.217.19.238"
local-data-ptr: "172.217.19.238 manifest.googlevideo.com"

local-zone: "1e100.net" redirect
local-data: "1e100.net A 127.0.0.1"

include: "/tmp/mnt/sda1/Backups/jffs/unbound/youtube.conf" #Relocate accordingly your system.


python:
remote-control:

## STEP FOUR: DOWNLOAD UPDATED YOUTUBE.CONF FILE

Add custom script to your router. This code pulls regenerated Youtube Ad block rules from PHP file.

#!/bin/bash

#PULL YOUTUBE AD BLOCK
youtubeconfig=/tmp/mnt/sda1/Backups/jffs/unbound/youtube.conf
youtube="https://YOUR_HOSTING_GOES_HERE/youtube.php" 
youtubeads=$( curl -s --insecure "$youtube" -o  "$youtubeconfig")


## STEP FIVE: ADD CRONJOB TO RUN CUSTOM.SH

*/15 * * * * root sh /tmp/custom.sh #Runs every 15 minutes, You can change it.


That's it. No more Youtube Video ADs will show up.
