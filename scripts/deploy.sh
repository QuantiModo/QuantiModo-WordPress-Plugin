#!/usr/bin/env bash
if [[ -z ${WP_ORG_PASSWORD} ]]; then echo "Please set WP_ORG_PASSWORD env" && exit 1; fi
if [[ -z ${WP_ORG_USERNAME} ]]; then echo "Please set WP_ORG_USERNAME env" && exit 1; fi
PLUGIN_SLUG="quantimodo"
CURRENT_DIR=`pwd`
MAIN_FILE="$PLUGIN_SLUG.php" # this should be the name of your main php file in the wordpress plugin
GIT_PATH="$CURRENT_DIR" # this file should be in the base of your git repository
SVN_PATH="/tmp/$PLUGIN_SLUG" # path to a temp SVN repo. No trailing slash required and don't add trunk.
SVN_URL="http://plugins.svn.wordpress.org/$PLUGIN_SLUG/" # Remote SVN repo on wordpress.org, with no trailing slash
COMMIT_MSG="Deploy to WordPress.org via Jenkins"
echo ".........................................."
echo "Preparing to deploy wordpress plugin"
echo ".........................................."
# Check version in readme.txt is the same as plugin file
NEW_VERSION1=`grep "^Stable tag" ${GIT_PATH}/README.md | awk -F':' '{print $2}' | tr -d ' '`
echo "readme version: $NEW_VERSION1"
NEW_VERSION2=`grep "Version:" ${GIT_PATH}/${MAIN_FILE} | awk -F':' '{print $2}' | tr -d ' '`
echo "$MAIN_FILE version: $NEW_VERSION2"
if [[ "$NEW_VERSION1" != "$NEW_VERSION2" ]]; then echo "Versions don't match. Exiting...."; exit 1; fi
echo "Versions match in readme.txt and PHP file. Let's proceed..."
echo
echo "Creating local copy of SVN repo ..."
svn co ${SVN_URL} ${SVN_PATH}
echo "Exporting the HEAD of master from git to the trunk of SVN"
git checkout-index -a -f --prefix=${SVN_PATH}/trunk/
echo "Ignoring github specific & deployment script"
svn propset svn:ignore "deploy.sh
.git
.gitignore" "$SVN_PATH/trunk/"
if [[ ! -d "$SVN_PATH/assets/" ]]; then
	echo "Moving assets-wp-repo"
	mkdir ${SVN_PATH}/assets/
	mv ${SVN_PATH}/trunk/assets-wp-repo/* ${SVN_PATH}/assets/
	svn add ${SVN_PATH}/assets/
	svn delete ${SVN_PATH}/trunk/assets-wp-repo
fi
# Create WP.org readme.txt
if [[ -f "${SVN_PATH}/trunk/README.md" ]]; then
	mv "${SVN_PATH}/trunk/README.md" "${SVN_PATH}/trunk/readme.txt"
	sed -i.bak \
		-e 's/^# \(.*\)$/=== \1 ===/' \
		-e 's/ #* ===$/ ===/' \
		-e 's/^## \(.*\)$/== \1 ==/' \
		-e 's/ #* ==$/ ==/' \
		-e 's/^### \(.*\)$/= \1 =/' \
		-e 's/ #* =$/ =/' \
		"${SVN_PATH}/trunk/readme.txt"
	# Remove the sed backup file
	rm "${SVN_PATH}/trunk/readme.txt.bak"
fi
echo "Changing directory to SVN"
cd ${SVN_PATH}/trunk/
# Add all new files that are not set to be ignored
echo "committing to trunk"
svn commit --username=${WP_ORG_USERNAME} --password=${WP_ORG_PASSWORD} -m "$COMMIT_MSG" --no-auth-cache
echo "Updating WP plugin repo assets & committing"
cd ${SVN_PATH}/assets/
svn commit --username=${WP_ORG_USERNAME} --password=${WP_ORG_PASSWORD} -m "Updating wp-repo-assets" --no-auth-cache
echo "Check if tagged version $NEW_VERSION1 exists"
cd ${SVN_PATH}
if [[ ! -d "$SVN_PATH/tags/$NEW_VERSION1/" ]];
    then
        echo "Creating new SVN tag & committing it"
        svn copy trunk/ tags/${NEW_VERSION1}/
        cd ${SVN_PATH}/tags/${NEW_VERSION1}
        if [[ ! -f includes/integration.js ]]; then
            echo "integration.js not found!" && exit 1;
        fi
        svn commit --username=${WP_ORG_USERNAME} --password=${WP_ORG_PASSWORD} -m "Tagging version $NEW_VERSION1" --no-auth-cache
    else
        echo "tagged version $NEW_VERSION1 already exists!"
        exit 1
fi
echo "Removing temporary directory $SVN_PATH"
rm -fr ${SVN_PATH}/
echo "*** FIN ***"