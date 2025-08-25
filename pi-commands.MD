# Update in-place with git pull

Go into the repo folder and pull the latest:
ssh arian@10.0.0.139   # (or your Pi's hostname/IP)
cd ~/hcc-csa-website
git pull origin main   # or master, depending on your default branch

Redeploy:
sudo rm -rf /var/www/html/*
sudo cp -r ~/hcc-csa-website/* /var/www/html/
sudo chown -R www-data:www-data /var/www/html