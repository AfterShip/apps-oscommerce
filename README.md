### Usage

Support auto generate CSV by cronjob, so that aftership can fetch the tracking number or order info by using the auto-fetch apps.

https://www.aftership.com/apps/auto-fetch

### Config

- DATE_RANGE: default 3 days
- ORDER_STATUS: default id 4
- CSV_FILE_PATH: default in the folder aftership
- CSV_FILE_NAME: default filename aftership.csv


### Upload
upload the folder `aftership` in to your public root folder

```
/your_web_root_dir/public_html/
```

then 

```
chmod 666 /your_web_root_dir/public_html/shipment.csv
```


### cronjob
Setup the cronjob for running the "export-aftership-csv.php"

It will generate the CSV file everyday on 6:30pm (your linux server time)

```
30 18 * * * /your_web_root_dir/public_html/aftership/export-aftership-csv.php

```

### Setup the Auto Fetch Job in aftership.com
* Login to your account, go to "Apps"

https://www.aftership.com/apps/auto-fetch

* Install the  "Auto Fetch"

follow the instruction and save the value.


### Done~

