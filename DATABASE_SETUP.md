# HealthPaws Database Setup Guide

This guide will help you set up the HealthPaws veterinary management system database and get the dashboard running.

## Prerequisites

- XAMPP installed and running (Apache + MySQL)
- PHP 7.4 or higher
- MySQL 5.7 or higher

## Database Setup

### Step 1: Start XAMPP Services
1. Open XAMPP Control Panel
2. Start Apache and MySQL services
3. Ensure both services are running (green status)

### Step 2: Create Database
1. Open your web browser and go to `http://localhost/phpmyadmin`
2. Click on "New" to create a new database
3. Enter database name: `healthpaws`
4. Click "Create"

### Step 3: Import Database Schema
1. In phpMyAdmin, select the `healthpaws` database
2. Click on the "Import" tab
3. Click "Choose File" and select `database/schema.sql`
4. Click "Go" to import the schema

### Step 4: Import Sample Data
1. Still in the `healthpaws` database, click "Import" again
2. Select `database/sample_data.sql`
3. Click "Go" to import the sample data

## Configuration

### Database Connection
The database connection is configured in `config/database.php`. Default settings are:
- Host: `localhost`
- Database: `healthpaws`
- Username: `root`
- Password: `` (empty)

If you have different MySQL credentials, update the file accordingly.

## Running the Dashboard

### Step 1: Access the Dashboard
1. Open your web browser
2. Navigate to: `http://localhost/healthpaws2/dashboard.php`

### Step 2: Test Different Clinic Views
You can test different clinic subdomains by adding URL parameters:
- Demo clinic: `dashboard.php?subdomain=demo&clinic=Demo Veterinary Clinic`
- Happy Tails: `dashboard.php?subdomain=happytails&clinic=Happy Tails Veterinary Clinic`
- Paw Care: `dashboard.php?subdomain=pawcare&clinic=Paw Care Animal Hospital`

## Dashboard Features

### Available Tabs
1. **Overview** - Dashboard summary with statistics and upcoming appointments
2. **Appointments** - Manage all appointments with search and actions
3. **Calendar** - Monthly calendar view with appointment indicators
4. **Patients** - View and manage patient records
5. **Billing** - Track invoices and payments
6. **Inventory** - Manage clinic supplies and stock levels
7. **Reports** - Analytics and business insights

### Database Tables
The system includes the following main tables:
- `User` - User accounts and authentication
- `Clinic` - Veterinary clinic information
- `Pet` - Patient pet records
- `Owner` - Pet owner information
- `Appointment` - Scheduling and appointments
- `MedicalRecord` - Health records and diagnoses
- `Vaccination` - Vaccine tracking
- `Payment` - Billing and payment records
- `Veterinarian` - Staff veterinarian information

## Troubleshooting

### Common Issues

1. **Database Connection Error**
   - Verify MySQL is running in XAMPP
   - Check database credentials in `config/database.php`
   - Ensure database `healthpaws` exists

2. **Page Not Found**
   - Verify Apache is running in XAMPP
   - Check file paths and permissions
   - Ensure files are in the correct XAMPP htdocs directory

3. **Database Query Errors**
   - Check if all tables were created successfully
   - Verify sample data was imported
   - Check MySQL error logs in XAMPP

### Error Logs
- Apache logs: `xampp/apache/logs/error.log`
- MySQL logs: `xampp/mysql/data/mysql_error.log`

## Next Steps

After successful setup, you can:
1. Customize the dashboard styling in the CSS section
2. Add more functionality to the JavaScript functions
3. Create additional forms for data entry
4. Implement user authentication and role-based access
5. Add more detailed reporting and analytics

## Support

If you encounter issues:
1. Check the troubleshooting section above
2. Verify all prerequisites are met
3. Check XAMPP service status
4. Review error logs for specific error messages

The dashboard is now ready to use with a fully functional database backend!


