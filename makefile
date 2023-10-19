include .env

.PHONY: default

plg_content_faicons.zip: faicons.php faicons.xml language/**/*.ini
	zip -rq9 $@ $^

build:
	@echo "Building plg_content_faicons.zip"
	@make plg_content_faicons.zip

version:
	@echo "Version: $(VERSION)"
	git checkout master
	git fetch
	git pull
	xmlstarlet ed --omit-decl -S -u "/extension/version" -v ${VERSION} faicons.xml > faicons.xml.tmp && cat faicons.xml.tmp > faicons.xml
	xmlstarlet ed --omit-decl \
		-s "/updates" -t elem -n "update" \
		-s "/updates/update[last()]" -t elem -n "name" -v "Font Awesome icons" \
		-s "/updates/update[last()]" -t elem -n "description" -v "Font Awesome icons snippet code" \
		-s "/updates/update[last()]" -t elem -n "element" -v "faicons" \
		-s "/updates/update[last()]" -t elem -n "type" -v "plugin" \
		-s "/updates/update[last()]" -t elem -n "version" -v "${VERSION}" \
		-s "/updates/update[last()]" -t elem -n "folder" -v "content" \
		-s "/updates/update[last()]" -t elem -n "client" -v "0" \
		-s "/updates/update[last()]" -t elem -n "infourl" \
		-s "/updates/update[last()]/infourl" -t attr -n "title" -v "Font Awesome icons" \
		-s "/updates/update[last()]" -t elem -n "downloads" \
		-s "/updates/update[last()]/downloads" -t elem -n "downloadurl" -v "https://extension-updates.com/updates/zip/r2h/faicons/plg_content_faicons_${VERSION}.zip" \
		-s "/updates/update[last()]/downloads/downloadurl" -t attr -n "type" -v "full" \
		-s "/updates/update[last()]/downloads/downloadurl" -t attr -n "format" -v "zip" \
		-s "/updates/update[last()]" -t elem -n "tags" \
		-s "/updates/update[last()]/tags" -t elem -n "tag" -v "stable" \
		-s "/updates/update[last()]" -t elem -n "maintainer" -v "R2H BV" \
		-s "/updates/update[last()]" -t elem -n "maintainerurl" -v "https://extension-updates.com/" \
		-s "/updates/update[last()]" -t elem -n "targetplatform" \
		-s "/updates/update[last()]/targetplatform" -t attr -n "name" -v "joomla" \
		-s "/updates/update[last()]/targetplatform" -t attr -n "version" -v "[45].[0123456789]" updates/faicons.xml > updates/faicons.xml.tmp
	cat updates/faicons.xml.tmp > updates/faicons.xml
	rm -f faicons.xml.tmp updates/faicons.xml.tmp
	@make plg_content_faicons.zip
	mv plg_content_faicons.zip updates/dist/plg_content_faicons_${VERSION}.zip
	@ftp-upload -h ${FTP_HOST} -u ${FTP_USER} --password ${FTP_PASSWORD} -d "${FTP_BASEPATH}/updates/zip/r2h/faicons/" updates/dist/plg_content_faicons_${VERSION}.zip
	@ftp-upload -h ${FTP_HOST} -u ${FTP_USER} --password ${FTP_PASSWORD} -d "${FTP_BASEPATH}/updates/r2h/" updates/faicons.xml
	git add mod_rbs4custom.xml updates/customhtml.xml
	git commit -m "Updated version to ${VERSION}"
	git tag -a ${VERSION} -m "Version ${VERSION}"
	git push origin main --tags
