File structure

poopyipsum.com (45.55.82.162)
/var/www/timesheet/
├── frontend/
│   ├── index.html          # Main timesheet app (with auth checks)
│   └── login.php           # Login page
└── api/
    ├── auth.php            # Authentication helper
    ├── entries.php         # Timesheet entries API (protected)
    └── settings.php        # Settings API (protected)

/etc/nginx/sites-available/
└── poopyipsum             # Nginx config file

/etc/nginx/sites-enabled/
└── poopyipsum -> ../sites-available/poopyipsum

Database: timekeeping
├── entries table
├── settings table
└── User: timekeeping / Password: BPbhZuZGR&sZZ7XzGCKJ!