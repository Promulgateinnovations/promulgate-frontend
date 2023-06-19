
 # Setup Requirements!  
  
 - Web Server - Apache
 - PHP - 7.3  
 - Dependency Manager for PHP(Composer)  
  
 ## Installation:  
  
 - Setup Apache & PHP  
 - Install PHP composer for dependencies used, download it from https://getcomposer.org/  
 - Once composer is successfully installed in machine from root directory of frontend i.,e from `/frontend/` run `composer install` for downloading all required dependencies/libraries used for the application  
 - `composer install` will download all dependencies & places them in `vendor` directory  
   > `vendor` directory won't be available in **GIT**  
   >> Composer related settings an be skipped for now as I have pushed vendor folder also to GIT so that each of us don't need to spend time on setup issues 
  
 - Create a virtual host in apache & point domain to `/frontend` directory  
   
- That's it once above steps are done we can test the configured URL in browser

 # NOTES:  
  
 ## ENV file  
 - We have all the configuration related to the site in `.env` file which is located in root directory  of frontend
	 > Due to security reasons production settings won't be available in `.env` file available in  **GIT**  
- Based on machine we need to change settings
  
 ## Updates/Deployments  
 - Whenever we plan to deploy/test new code changes we must make sure our dependencies are always *up to date* in any machine either local or development or production for that we must always run `composer update`  to fetch & update dependencies
