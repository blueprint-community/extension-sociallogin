#!/bin/bash

DIR="{root}"
DATADIR="{root/data}"

composer require laravel/socialite

/bin/cp -f "$DATADIR/SocialConnection.php" "$DIR/app/Models/SocialConnection.php"
/bin/cp -f "$DATADIR/SocialProvider.php" "$DIR/app/Models/SocialProvider.php"
