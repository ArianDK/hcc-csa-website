#!/usr/bin/env python3
"""
CSA Member Export Utility
Exports member data to CSV for admin use
Requires Python 3.6+ and mysql-connector-python (or uses sqlite3 for SQLite)
"""

import csv
import os
import sys
from datetime import datetime
import argparse

# Try to import database libraries
try:
    import mysql.connector
    MYSQL_AVAILABLE = True
except ImportError:
    MYSQL_AVAILABLE = False

import sqlite3  # Built-in, always available

def load_config():
    """Load database configuration from PHP config file."""
    config_path = os.path.join(os.path.dirname(__file__), '..', 'config', 'config.php')
    
    # Simple PHP config parser (for demo - in production, use a proper PHP parser)
    config = {
        'db': {
            'driver': 'mysql',
            'host': 'localhost',
            'name': 'csa',
            'user': 'csa_user',
            'pass': 'REPLACE_ME',
            'charset': 'utf8mb4'
        }
    }
    
    try:
        with open(config_path, 'r') as f:
            content = f.read()
            
        # Extract database config (basic parsing)
        if "'driver' => 'sqlite'" in content:
            config['db']['driver'] = 'sqlite'
            # Look for path
            if 'path' in content:
                config['db']['path'] = '/path/to/csa.sqlite'  # Default
                
    except FileNotFoundError:
        print("Warning: Config file not found, using defaults")
    
    return config

def connect_mysql(config):
    """Connect to MySQL database."""
    if not MYSQL_AVAILABLE:
        raise Exception("mysql-connector-python not installed. Install with: pip install mysql-connector-python")
    
    return mysql.connector.connect(
        host=config['db']['host'],
        database=config['db']['name'],
        user=config['db']['user'],
        password=config['db']['pass'],
        charset=config['db']['charset']
    )

def connect_sqlite(config):
    """Connect to SQLite database."""
    db_path = config['db'].get('path', 'data/csa.sqlite')
    if not os.path.exists(db_path):
        raise Exception(f"SQLite database not found at: {db_path}")
    
    return sqlite3.connect(db_path)

def export_members(output_file, status_filter=None, include_blocked=False):
    """Export member data to CSV."""
    config = load_config()
    
    # Connect to database
    if config['db']['driver'] == 'sqlite':
        conn = connect_sqlite(config)
        cursor = conn.cursor()
        cursor.row_factory = sqlite3.Row  # Dict-like access
    else:
        conn = connect_mysql(config)
        cursor = conn.cursor(dictionary=True)
    
    try:
        # Build query
        query = """
            SELECT 
                id,
                first_name,
                last_name,
                email,
                major,
                campus,
                consent_comms,
                accepted_code,
                status,
                verified_at,
                created_at,
                updated_at
            FROM members
        """
        
        conditions = []
        params = []
        
        if status_filter:
            conditions.append("status = ?")
            params.append(status_filter.upper())
        
        if not include_blocked:
            conditions.append("status != ?")
            params.append("BLOCKED")
        
        if conditions:
            query += " WHERE " + " AND ".join(conditions)
        
        query += " ORDER BY created_at DESC"
        
        # Execute query
        cursor.execute(query, params)
        members = cursor.fetchall()
        
        if not members:
            print("No members found matching the criteria.")
            return False
        
        # Write CSV
        with open(output_file, 'w', newline='', encoding='utf-8') as csvfile:
            fieldnames = [
                'ID', 'First Name', 'Last Name', 'Email', 'Major', 'Campus',
                'Email Consent', 'Code Accepted', 'Status', 'Verified Date',
                'Join Date', 'Last Updated'
            ]
            
            writer = csv.DictWriter(csvfile, fieldnames=fieldnames)
            writer.writeheader()
            
            for member in members:
                # Handle date formatting
                verified_date = member['verified_at']
                if verified_date:
                    if isinstance(verified_date, str):
                        try:
                            verified_date = datetime.fromisoformat(verified_date.replace('Z', '+00:00'))
                        except:
                            pass
                    verified_date = verified_date.strftime('%Y-%m-%d %H:%M:%S') if verified_date else ''
                else:
                    verified_date = ''
                
                join_date = member['created_at']
                if isinstance(join_date, str):
                    try:
                        join_date = datetime.fromisoformat(join_date.replace('Z', '+00:00'))
                    except:
                        pass
                join_date = join_date.strftime('%Y-%m-%d %H:%M:%S') if join_date else ''
                
                updated_date = member['updated_at']
                if isinstance(updated_date, str):
                    try:
                        updated_date = datetime.fromisoformat(updated_date.replace('Z', '+00:00'))
                    except:
                        pass
                updated_date = updated_date.strftime('%Y-%m-%d %H:%M:%S') if updated_date else ''
                
                writer.writerow({
                    'ID': member['id'],
                    'First Name': member['first_name'],
                    'Last Name': member['last_name'],
                    'Email': member['email'],
                    'Major': member['major'] or '',
                    'Campus': member['campus'] or '',
                    'Email Consent': 'Yes' if member['consent_comms'] else 'No',
                    'Code Accepted': 'Yes' if member['accepted_code'] else 'No',
                    'Status': member['status'],
                    'Verified Date': verified_date,
                    'Join Date': join_date,
                    'Last Updated': updated_date
                })
        
        print(f"Successfully exported {len(members)} members to {output_file}")
        return True
        
    finally:
        cursor.close()
        conn.close()

def main():
    parser = argparse.ArgumentParser(description='Export CSA member data to CSV')
    parser.add_argument('--output', '-o', 
                       default=f'members-{datetime.now().strftime("%Y%m%d")}.csv',
                       help='Output CSV file name')
    parser.add_argument('--status', '-s',
                       choices=['PENDING', 'VERIFIED', 'BLOCKED'],
                       help='Filter by member status')
    parser.add_argument('--include-blocked', '-b',
                       action='store_true',
                       help='Include blocked members in export')
    parser.add_argument('--verified-only', '-v',
                       action='store_true',
                       help='Export only verified members')
    
    args = parser.parse_args()
    
    # Handle shortcuts
    if args.verified_only:
        args.status = 'VERIFIED'
    
    try:
        success = export_members(
            output_file=args.output,
            status_filter=args.status,
            include_blocked=args.include_blocked
        )
        
        if success:
            print(f"\n📊 Export completed successfully!")
            print(f"📁 File: {args.output}")
            print(f"📈 Use this data for:")
            print("   • Membership reports")
            print("   • Email marketing lists")
            print("   • Academic analysis")
            print("   • Event planning")
            
            # Show basic stats
            with open(args.output, 'r') as f:
                line_count = sum(1 for line in f) - 1  # Subtract header
            print(f"📋 Total records: {line_count}")
        else:
            sys.exit(1)
            
    except Exception as e:
        print(f"❌ Export failed: {e}")
        print("\n🔧 Troubleshooting:")
        print("   • Check database connection settings")
        print("   • Ensure database server is running")
        print("   • Verify Python dependencies are installed")
        print("   • Check file permissions for output directory")
        sys.exit(1)

if __name__ == '__main__':
    main()
