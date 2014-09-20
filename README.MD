WoW Utils
========

This repository contain some tools for WoW Development.


## Wpp extractor.php

The file "wpp extractor.php" is a tool that extract the 'creatures spawns' sql code from a file sql that the [WoW Packet Parser](https://github.com/TrinityCore/WowPacketParser) extracted from a file.pkt
before to do this, it checks if the creatures exists in your database or if they are already fixed to extract ONLY the 'creatures spawns' that aren't fixed in your database.

## Usage

# How to use

First of all, the toolt must be configured, so open the file config.php and set the username, password and database of your local db.

Well, I made this tool to execute ti from terminal, so to use it go to the directory of wpp extractor.php and write:

php "wpp extractor.php"

press enter, the tool will ask you the path of the file sql extracted with the WoW Packet Parser and the path of the file that will contains the clean fix of the 'spawns'.

exmample:

```
$ php "wpp extractor.php"
Path of the extracted sql: wpp_file.sql
Output of the file sql:  fix.sql
```

Now the tool will print the entries already spawned or the entries that not exist in the table creature_template and will make a file named fix.sql with the fix of the 'creature spawns'.