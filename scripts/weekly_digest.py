#!/usr/bin/env python3
"""
CSA Weekly Digest Email Utility
Sends weekly summary emails to CSA officers
Uses only Python standard library - no external dependencies required
"""

import smtplib
import sqlite3
import os
import sys
from datetime import datetime, timedelta
from email.mime.text import MIMEText
from email.mime.multipart import MIMEMultipart
from email.mime.base import MIMEBase
from email import encoders
import argparse

# Try to import mysql connector
try:
    import mysql.connector
    MYSQL_AVAILABLE = True
except ImportError:
    MYSQL_AVAILABLE = False

def load_config():
    """Load configuration from PHP config file."""
    # In a real implementation, you'd parse the PHP config properly
    # For this demo, we'll use hardcoded values that match the PHP config structure
    return {
        'db': {
            'driver': 'mysql',  # or 'sqlite'
            'host': 'localhost',
            'name': 'csa',
            'user': 'csa_user',
            'pass': 'REPLACE_ME',
            'path': 'data/csa.sqlite'  # for SQLite
        },
        'smtp': {
            'host': 'smtp.gmail.com',
            'port': 587,
            'user': 'no-reply@example.com',
            'pass': 'REPLACE_ME',
            'from_email': 'no-reply@example.com',
            'from_name': 'CSA at HCC'
        },
        'app': {
            'name': 'Computer Science Association',
            'admin_email': 'president@hccs.edu'
        }
    }

def connect_database(config):
    """Connect to the database."""
    if config['db']['driver'] == 'sqlite':
        db_path = config['db']['path']
        if not os.path.exists(db_path):
            raise Exception(f"SQLite database not found: {db_path}")
        
        conn = sqlite3.connect(db_path)
        conn.row_factory = sqlite3.Row
        return conn, conn.cursor()
    
    else:  # MySQL
        if not MYSQL_AVAILABLE:
            raise Exception("mysql-connector-python not installed")
        
        conn = mysql.connector.connect(
            host=config['db']['host'],
            database=config['db']['name'],
            user=config['db']['user'],
            password=config['db']['pass']
        )
        return conn, conn.cursor(dictionary=True)

def get_weekly_stats(cursor, config):
    """Gather statistics for the past week."""
    # Calculate date range
    end_date = datetime.now()
    start_date = end_date - timedelta(days=7)
    
    stats = {
        'period': {
            'start': start_date.strftime('%Y-%m-%d'),
            'end': end_date.strftime('%Y-%m-%d')
        }
    }
    
    # New registrations this week
    if config['db']['driver'] == 'sqlite':
        cursor.execute("""
            SELECT COUNT(*) as count 
            FROM members 
            WHERE created_at >= date('now', '-7 days')
        """)
    else:
        cursor.execute("""
            SELECT COUNT(*) as count 
            FROM members 
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
        """)
    
    stats['new_registrations'] = cursor.fetchone()['count']
    
    # New verifications this week
    if config['db']['driver'] == 'sqlite':
        cursor.execute("""
            SELECT COUNT(*) as count 
            FROM members 
            WHERE verified_at >= date('now', '-7 days')
        """)
    else:
        cursor.execute("""
            SELECT COUNT(*) as count 
            FROM members 
            WHERE verified_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
        """)
    
    stats['new_verifications'] = cursor.fetchone()['count']
    
    # Total member counts
    cursor.execute("SELECT status, COUNT(*) as count FROM members GROUP BY status")
    status_counts = {}
    for row in cursor.fetchall():
        status_counts[row['status']] = row['count']
    
    stats['total_members'] = status_counts
    
    # Pending verifications (needs attention)
    stats['pending_verifications'] = status_counts.get('PENDING', 0)
    
    # Popular majors for new members this week
    if config['db']['driver'] == 'sqlite':
        cursor.execute("""
            SELECT major, COUNT(*) as count 
            FROM members 
            WHERE major IS NOT NULL 
            AND major != '' 
            AND created_at >= date('now', '-7 days')
            GROUP BY major 
            ORDER BY count DESC 
            LIMIT 5
        """)
    else:
        cursor.execute("""
            SELECT major, COUNT(*) as count 
            FROM members 
            WHERE major IS NOT NULL 
            AND major != '' 
            AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
            GROUP BY major 
            ORDER BY count DESC 
            LIMIT 5
        """)
    
    stats['new_member_majors'] = cursor.fetchall()
    
    # Email consent rate for new members
    if config['db']['driver'] == 'sqlite':
        cursor.execute("""
            SELECT 
                SUM(CASE WHEN consent_comms = 1 THEN 1 ELSE 0 END) as consented,
                COUNT(*) as total
            FROM members 
            WHERE created_at >= date('now', '-7 days')
        """)
    else:
        cursor.execute("""
            SELECT 
                SUM(CASE WHEN consent_comms = 1 THEN 1 ELSE 0 END) as consented,
                COUNT(*) as total
            FROM members 
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
        """)
    
    consent_data = cursor.fetchone()
    stats['email_consent'] = {
        'consented': consent_data['consented'] or 0,
        'total': consent_data['total'] or 0,
        'rate': round((consent_data['consented'] or 0) / max(consent_data['total'] or 1, 1) * 100, 1)
    }
    
    return stats

def generate_email_content(stats):
    """Generate the email content."""
    # Text version
    text_content = f"""
CSA Weekly Digest
{stats['period']['start']} to {stats['period']['end']}

📊 MEMBERSHIP SUMMARY
• New registrations: {stats['new_registrations']}
• New verifications: {stats['new_verifications']}
• Pending verifications: {stats['pending_verifications']}

📈 TOTAL MEMBERSHIP
• Verified: {stats['total_members'].get('VERIFIED', 0)}
• Pending: {stats['total_members'].get('PENDING', 0)}
• Blocked: {stats['total_members'].get('BLOCKED', 0)}
• Total: {sum(stats['total_members'].values())}

🎓 NEW MEMBER MAJORS
"""

    for major_data in stats['new_member_majors']:
        text_content += f"• {major_data['major']}: {major_data['count']}\n"
    
    if not stats['new_member_majors']:
        text_content += "• No new members this week\n"

    text_content += f"""
📧 EMAIL CONSENT
• {stats['email_consent']['consented']} of {stats['email_consent']['total']} new members ({stats['email_consent']['rate']}%) opted in for emails

🔔 ACTION ITEMS
"""

    if stats['pending_verifications'] > 0:
        text_content += f"• Review {stats['pending_verifications']} pending verifications\n"
    
    text_content += """• Check Discord for member questions
• Plan upcoming events and workshops
• Follow up on any recent event feedback

---
This is an automated weekly digest from the CSA website.
Generated on """ + datetime.now().strftime('%Y-%m-%d at %H:%M:%S') + """

Computer Science Association
Houston Community College
"""

    # HTML version
    html_content = f"""
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>CSA Weekly Digest</title>
    <style>
        body {{ font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }}
        .header {{ background: #1a365d; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }}
        .content {{ background: #f7fafc; padding: 20px; }}
        .stat-grid {{ display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; margin: 20px 0; }}
        .stat-card {{ background: white; padding: 15px; border-radius: 6px; text-align: center; border-left: 4px solid #3182ce; }}
        .stat-number {{ font-size: 2em; font-weight: bold; color: #1a365d; }}
        .stat-label {{ color: #666; font-size: 0.9em; }}
        .section {{ margin: 20px 0; }}
        .section h3 {{ color: #1a365d; border-bottom: 2px solid #e2e8f0; padding-bottom: 5px; }}
        .major-list {{ background: white; padding: 15px; border-radius: 6px; }}
        .action-items {{ background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 6px; }}
        .footer {{ background: #edf2f7; padding: 15px; text-align: center; font-size: 0.8em; color: #666; border-radius: 0 0 8px 8px; }}
    </style>
</head>
<body>
    <div class="header">
        <h1>CSA Weekly Digest</h1>
        <p>{stats['period']['start']} to {stats['period']['end']}</p>
    </div>
    
    <div class="content">
        <div class="section">
            <h3>📊 This Week's Activity</h3>
            <div class="stat-grid">
                <div class="stat-card">
                    <div class="stat-number">{stats['new_registrations']}</div>
                    <div class="stat-label">New Registrations</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">{stats['new_verifications']}</div>
                    <div class="stat-label">New Verifications</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">{stats['pending_verifications']}</div>
                    <div class="stat-label">Pending Review</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">{sum(stats['total_members'].values())}</div>
                    <div class="stat-label">Total Members</div>
                </div>
            </div>
        </div>
        
        <div class="section">
            <h3>🎓 New Member Majors</h3>
            <div class="major-list">
"""

    if stats['new_member_majors']:
        for major_data in stats['new_member_majors']:
            html_content += f"<p><strong>{major_data['major']}</strong>: {major_data['count']} student(s)</p>"
    else:
        html_content += "<p><em>No new members this week</em></p>"

    html_content += f"""
            </div>
        </div>
        
        <div class="section">
            <h3>📧 Email Engagement</h3>
            <p><strong>{stats['email_consent']['consented']}</strong> of <strong>{stats['email_consent']['total']}</strong> new members ({stats['email_consent']['rate']}%) opted in for email updates.</p>
        </div>
"""

    if stats['pending_verifications'] > 0 or stats['new_registrations'] > 0:
        html_content += f"""
        <div class="section">
            <h3>🔔 Action Items</h3>
            <div class="action-items">
"""
        if stats['pending_verifications'] > 0:
            html_content += f"<p>• <strong>Review {stats['pending_verifications']} pending verifications</strong> in the admin dashboard</p>"
        
        html_content += """
                <p>• Check Discord for member questions and engagement</p>
                <p>• Plan upcoming events and workshops based on member interests</p>
                <p>• Follow up on any recent event feedback</p>
            </div>
        </div>
"""

    html_content += f"""
    </div>
    
    <div class="footer">
        <p>This is an automated weekly digest from the CSA website.<br>
        Generated on {datetime.now().strftime('%Y-%m-%d at %H:%M:%S')}</p>
        <p><strong>Computer Science Association</strong><br>Houston Community College</p>
    </div>
</body>
</html>
"""

    return text_content, html_content

def send_email(config, subject, text_content, html_content, recipients):
    """Send the digest email."""
    msg = MIMEMultipart('alternative')
    msg['Subject'] = subject
    msg['From'] = f"{config['smtp']['from_name']} <{config['smtp']['from_email']}>"
    msg['To'] = ', '.join(recipients)
    
    # Add text and HTML parts
    text_part = MIMEText(text_content, 'plain')
    html_part = MIMEText(html_content, 'html')
    
    msg.attach(text_part)
    msg.attach(html_part)
    
    # Send email
    server = smtplib.SMTP(config['smtp']['host'], config['smtp']['port'])
    server.starttls()
    server.login(config['smtp']['user'], config['smtp']['pass'])
    
    for recipient in recipients:
        server.sendmail(config['smtp']['from_email'], recipient, msg.as_string())
    
    server.quit()

def main():
    parser = argparse.ArgumentParser(description='Send CSA weekly digest email')
    parser.add_argument('--dry-run', '-d', action='store_true',
                       help='Generate content but don\'t send email')
    parser.add_argument('--recipients', '-r', nargs='+',
                       help='Email recipients (default: admin email from config)')
    parser.add_argument('--save-html', '-s', 
                       help='Save HTML content to file for preview')
    
    args = parser.parse_args()
    
    try:
        # Load configuration
        config = load_config()
        
        # Connect to database
        conn, cursor = connect_database(config)
        
        try:
            # Get weekly statistics
            stats = get_weekly_stats(cursor, config)
            
            # Generate email content
            text_content, html_content = generate_email_content(stats)
            
            # Determine recipients
            recipients = args.recipients or [config['app']['admin_email']]
            
            # Subject line
            subject = f"CSA Weekly Digest - {stats['period']['start']} to {stats['period']['end']}"
            
            if args.save_html:
                with open(args.save_html, 'w', encoding='utf-8') as f:
                    f.write(html_content)
                print(f"HTML content saved to: {args.save_html}")
            
            if args.dry_run:
                print("=== DRY RUN MODE ===")
                print(f"Subject: {subject}")
                print(f"Recipients: {', '.join(recipients)}")
                print("\n=== TEXT CONTENT ===")
                print(text_content)
                print("\n=== EMAIL WOULD BE SENT ===")
            else:
                send_email(config, subject, text_content, html_content, recipients)
                print(f"✅ Weekly digest sent successfully to: {', '.join(recipients)}")
                print(f"📊 Summary: {stats['new_registrations']} new registrations, {stats['pending_verifications']} pending reviews")
            
        finally:
            cursor.close()
            conn.close()
            
    except Exception as e:
        print(f"❌ Failed to send weekly digest: {e}")
        print("\n🔧 Troubleshooting:")
        print("   • Check database connection settings")
        print("   • Verify SMTP configuration")
        print("   • Ensure email credentials are correct")
        print("   • Check network connectivity")
        sys.exit(1)

if __name__ == '__main__':
    main()
