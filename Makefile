APP_DIR=etunnel
PKG_NAME=app_etunnel

all: pkg srv

pkg: clean
	rm -rf $(APP_DIR)
	mkdir -p $(APP_DIR)
	cp -a www app.json .htaccess start.sh stop.sh etunnel_mng.py etunnel.sh $(APP_DIR)
	tar czvf $(PKG_NAME).tgz $(APP_DIR)
	rm -rf $(APP_DIR)

srv:
	cp app.json www_server/data/
	tar czvf $(PKG_NAME)_server.tgz www_server

clean:
	rm -fr www/data/*
	rm -f www_server/data/app.json
	rm -f $(PKG_NAME)*.tgz*
