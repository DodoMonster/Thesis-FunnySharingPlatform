#!/bin/sh

for(i=1;i<=35103;i+=5000);
do php import4399.php "type=device";
done

for(i=1;i<=1590070;i+=5000);
do php import4399.php "type=game";
done


do php import4399.php "type=server";
