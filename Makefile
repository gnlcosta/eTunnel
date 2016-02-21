APP_DIR=etunnel
PKG_NAME=app_etunnel
VERSION=`cat app.json | sed '/version/!d' | sed s/\"version\":\ //g | sed s/\"//g | sed s/\ //g | sed s/\,//g`

all: pkg srv

pkg: clean
	rm -rf $(APP_DIR)
	mkdir -p $(APP_DIR)
	mkdir -p www/app/models
	cp -a www app.json .htaccess start.sh stop.sh etunnel_mng.py etunnel.sh $(APP_DIR)
	tar czvf $(PKG_NAME)-$(VERSION).tgz $(APP_DIR)
	rm -rf $(APP_DIR)

srv:
	mkdir -p www_server/data/
	cp app.json www_server/data/
	cp server_template.json www_server/data/
	tar czvf $(PKG_NAME)_server-$(VERSION).tgz www_server

clean:
	rm -fr www/data/*
	rm -f www_server/data/app.json
	rm -f www_server/data/server_template.json
	rm -f $(PKG_NAME)*.tgz*
