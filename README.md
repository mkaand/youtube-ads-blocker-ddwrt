# Blocking All Youtube Video Advertisements
Youtube Video ADs blocker for DDWRT with Unbound DNS

Hi Everyone,

I was working on blocking all Youtube video ADs. I guess I found a solution and I would like to share with DDWRT community. For my tests my solution perfectly blocks all video ads on Youtube doesn't matter Mobile, Android TV or Web Browser.

Youtube pushes ADs thru thousands of hostnames. I blocked all of them via Unbound DNS. Official Youtube app shows yellow AD fractions on timeline of the video but video will never display. Here is the step by step tutorial.

Watch in actions (How it works)
https://dai.ly/x7q9jjm

## Requirments:

* DDWRT router with Unbound (Recursive DNS) feature (You can check SETUP screen. Make sure you will see and checked "Recursive DNS Resolving (Unbound)" feature on DDWRT). I use DD-WRT v3.0-r38060 std 12/20/18 firmware and WRT1900AC v1 router.
* PHP Hosting

There are several Youtube ADs hostnames lists on the internet. My code merges two of them. One dynamic (https://api.hackertarget.com/hostsearch/?q=googlevideo.com) other one static list. By the way JFFS is my external drive:
/tmp/mnt/sda1/Backups/jffs/unbound/named.cache


## STEP ONE: PREPARE PHP FILE FOR UNBOUND DNS

You can download my PHP script here https://github.com/mkaand/youtube-ads-blocker-ddwrt/blob/master/youtube.php This PHP file prepares a rules to block youtube ADs for Unbound DNS. Output of my PHP file like that:

local-data: "r1---sn-25ge7n76.googlevideo.com A 127.0.0.1"<br>
local-data: "r1.sn-25ge7n76.googlevideo.com A 127.0.0.1"<br>
local-data: "r2---sn-25ge7n76.googlevideo.com A 127.0.0.1"<br>
local-data: "r2.sn-25ge7n76.googlevideo.com A 127.0.0.1"<br>
local-data: "r3---sn-25ge7n76.googlevideo.com A 127.0.0.1"<br>
local-data: "r3.sn-25ge7n76.googlevideo.com A 127.0.0.1"<br>
local-data: "r4---sn-25ge7n76.googlevideo.com A 127.0.0.1"<br>
local-data: "r4.sn-25ge7n76.googlevideo.com A 127.0.0.1"<br>
local-data: "r5---sn-25ge7n76.googlevideo.com A 127.0.0.1"<br>

## STEP TWO: ADD STARTUP LINES

sleep 5<br>
stopservice unbound -f<br>
unbound -c /tmp/mnt/sda1/Backups/jffs/unbound/unbound.conf<br>

Above code stops Unbound service and starts with new configuration. You can change config file location accordingly. 

## STEP THREE: ADD FOLLOWING UNBOUND CONFIG FILE TO YOUR EXTERNAL DRIVE JFFS OR SOMEWHERE IN YOUR SYSTEM

Your local hosts and settings should be different than mine. So the best way just copy the original /tmp/unbound.conf file and add lines that I indicated below:

server:<br>
verbosity: 1<br>
interface: 0.0.0.0<br>
interface: ::0<br>
outgoing-num-tcp: 10<br>
incoming-num-tcp: 10<br>
msg-buffer-size: 8192<br>
msg-cache-size: 1m<br>
num-queries-per-thread: 30<br>
rrset-cache-size: 2m<br>
infra-cache-numhosts: 200<br>
username: ""<br>
pidfile: "/var/run/unbound.pid"<br>
root-hints: "/tmp/mnt/sda1/Backups/jffs/unbound/named.cache" #Download updated named.cache and relocate accordingly your system Download from here https://www.internic.net/domain/named.root<br>
target-fetch-policy: "2 1 0 0 0 0"<br>
harden-short-bufsize: yes<br>
harden-large-queries: yes<br>
auto-trust-anchor-file: "/etc/unbound/root.key"<br>
key-cache-size: 100k<br>
neg-cache-size: 10k<br>
num-threads: 2<br>
so-reuseport: yes<br>
msg-cache-slabs: 2<br>
rrset-cache-slabs: 2<br>
infra-cache-slabs: 2<br>
key-cache-slabs: 2<br>
outgoing-range: 462<br>
access-control: 127.0.0.0/8 allow<br>
access-control: 192.168.20.1/24 allow<br>
access-control: 192.168.21.1/24 allow<br>
local-data: "localhost A 127.0.0.1"<br>
local-data: "WRT1900AC A 192.168.20.1"<br>
local-data: "WRT1900AC.local A 192.168.20.1"<br>
local-data: "VPN-SERVER.local A 192.168.20.2"<br>
local-data: "WDMYCLOUD.local A 192.168.20.3"<br>
local-data: "ASUS-N550JK.local A 192.168.20.100"<br>
local-data: "COMPANY.local A 192.168.20.101"<br>
local-data: "PLAYSTATION-3.local A 192.168.20.104"<br>
local-data: "CANON-MG6450.local A 192.168.20.105"<br>
local-data: "SOFTETHER_VPN.local A 192.168.20.106"<br>
local-data: "XIAOMI-MIBOX.local A 192.168.20.107"<br>
local-data: "EP-3703.local A 192.168.20.108"<br>
local-data: "LGWEBOSTV.local A 192.168.20.109"<br>

#ADD FOLLOWING LINES TO YOUR ORIGINAL UNBOUND.CONF FILE

#Blocking Ad Server domains. Google's AdSense, DoubleClick and Yahoo
#account for a 70 percent share of all advertising traffic. Block them.
local-zone: "doubleclick.net" redirect<br>
local-data: "doubleclick.net A 127.0.0.1"<br>
local-zone: "ads.youtube.com" redirect<br>
local-data: "ads.youtube.com A 127.0.0.1"<br>
local-zone: "adserver.yahoo.com" redirect<br><br>
local-data: "adserver.yahoo.com A 127.0.0.1"<br>

local-zone: "manifest.googlevideo.com" redirect<br>
local-data: "manifest.googlevideo.com A 172.217.19.238"<br>
local-data-ptr: "172.217.19.238 manifest.googlevideo.com"<br>

local-zone: "1e100.net" redirect<br>
local-data: "1e100.net A 127.0.0.1"<br>

include: "/tmp/mnt/sda1/Backups/jffs/unbound/youtube.conf" #Relocate accordingly your system.<br>


python:<br>
remote-control:<br>


## STEP FOUR: DOWNLOAD UPDATED YOUTUBE.CONF FILE

Add custom script to your router. This code pulls regenerated Youtube Ad block rules from PHP file.

#!/bin/bash<br>
#PULL YOUTUBE AD BLOCK<br>
youtubeconfig=/tmp/mnt/sda1/Backups/jffs/unbound/youtube.conf<br>
youtube="https://YOUR_HOSTING_GOES_HERE/youtube.php"<br>
youtubeads=$( curl -s --insecure "$youtube" -o  "$youtubeconfig")<br>


## STEP FIVE: ADD CRONJOB TO RUN CUSTOM.SH

*/15 * * * * root sh /tmp/custom.sh #Runs every 15 minutes, You can change it.


That's it. No more Youtube Video ADs will show up.
