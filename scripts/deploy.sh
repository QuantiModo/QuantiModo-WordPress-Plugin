#!/usr/bin/env bash
# In your Jenkins job configuration, select "Add build step > Execute shell", and paste this script contents.
# Replace `______your-plugin-name______`, `______your-wp-username______` and `______your-wp-password______` as needed.


# main config
WP_ORG_USER="______your-wp-username______" # your WordPress.org username
WP_ORG_PASS="______your-wp-password______" # your WordPress.org password
PLUGINSLUG="______your-plugin-name______"
CURRENTDIR=`pwd`
MAINFILE="______your-plugin-name______.php" # this should be the name of your main php file in the wordpress plugin

# git config
GITPATH="$CURRENTDIR" # this file should be in the base of your git repository

# svn config
SVNPATH="/tmp/$PLUGINSLUG" # path to a temp SVN repo. No trailing slash required and don't add trunk.
SVNURL="http://plugins.svn.wordpress.org/______your-plugin-name______/" # Remote SVN repo on wordpress.org, with no trailing slash
COMMITMSG="Deploy to WordPress.org via Jenkins"


# Let's begin...
echo ".........................................."
echo
echo "Preparing to deploy wordpress plugin"
echo
echo ".........................................."
echo

# Check version in readme.txt is the same as plugin file
NEWVERSION1=`grep "^Stable tag" $GITPATH/readme.txt | awk -F':' '{print $2}' | tr -d ' '`
echo "readme version: $NEWVERSION1"
NEWVERSION2=`grep "Version:" $GITPATH/$MAINFILE | awk -F':' '{print $2}' | tr -d ' '`
echo "$MAINFILE version: $NEWVERSION2"

if [ "$NEWVERSION1" != "$NEWVERSION2" ]; then echo "Versions don't match. Exiting...."; exit 1; fi

echo "Versions match in readme.txt and PHP file. Let's proceed..."

echo
echo "Creating local copy of SVN repo ..."
svn co $SVNURL $SVNPATH

echo "Exporting the HEAD of master from git to the trunk of SVN"
git checkout-index -a -f --prefix=$SVNPATH/trunk/

echo "Ignoring github specific & deployment script"
svn propset svn:ignore "deploy.sh
README.md
.git
.gitignore" "$SVNPATH/trunk/"

if [ ! -d "$SVNPATH/assets/" ]; then
	echo "Moving assets-wp-repo"
	mkdir $SVNPATH/assets/
	mv $SVNPATH/trunk/assets-wp-repo/* $SVNPATH/assets/
	svn add $SVNPATH/assets/
	svn delete $SVNPATH/trunk/assets-wp-repo
fi

echo "Changing directory to SVN"
cd $SVNPATH/trunk/
# Add all new files that are not set to be ignored
echo "committing to trunk"
svn commit --username=$WP_ORG_USER --password=$WP_ORG_PASS -m "$COMMITMSG"

echo "Updating WP plugin repo assets & committing"
cd $SVNPATH/assets/
svn commit --username=$WP_ORG_USER --password=$WP_ORG_PASS -m "Updating wp-repo-assets"

echo "Check if tagged version exists"
cd $SVNPATH
if [ ! -d "$SVNPATH/tags/$NEWVERSION1/" ]; then
	echo "Creating new SVN tag & committing it"
	svn copy trunk/ tags/$NEWVERSION1/
	cd $SVNPATH/tags/$NEWVERSION1
	svn commit --username=$WP_ORG_USER --password=$WP_ORG_PASS -m "Tagging version $NEWVERSION1"
fi

echo "Removing temporary directory $SVNPATH"
rm -fr $SVNPATH/

echo "*** FIN ***"