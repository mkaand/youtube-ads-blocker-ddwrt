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

buraya

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
