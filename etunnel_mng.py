#!/usr/bin/python3

#    eTunnel
#    Copyright (C) 2015 Gianluca Costa <g.costa@xplico.org>
#
#    This program is free software: you can redistribute it and/or modify
#    it under the terms of the GNU Affero General Public License as
#    published by the Free Software Foundation, either version 3 of the
#    License, or (at your option) any later version.
#
#    This program is distributed in the hope that it will be useful,
#    but WITHOUT ANY WARRANTY; without even the implied warranty of
#    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#    GNU Affero General Public License for more details.
#
#    You should have received a copy of the GNU Affero General Public License
#    along with this program.  If not, see <http://www.gnu.org/licenses/>.


import errno
import threading
import time
import os
import json
import http.client
import hashlib
from uuid import getnode as get_mac

tmp_dir = '/tmp/'
config_dir = '/data/embed/etunnel/'
#config_dir = '/tmp/'
cfg_path = config_dir+'/etunnel_cfg.json'

os.system('mkdir -p '+config_dir)
os.system('chown -R www-data:www-data '+config_dir)

threadLock = threading.Lock()
stop_tunnel = True
threads = []
tunnel_state = 'off'

class myThread (threading.Thread):
    def __init__(self, threadID, name, counter):
        threading.Thread.__init__(self)
        self.threadID = threadID
        self.name = name
        self.counter = counter
    def run(self):
        print("Starting " + self.name)
        print_time(self.name, self.counter, 3)
        
def print_time(threadName, delay, counter):
    while counter:
        # Get lock to synchronize threads
        threadLock.acquire()
        time.sleep(delay)
        # Free lock to release next thread
        threadLock.release()
        print("%s: %s" % (threadName, time.ctime(time.time())))
        counter -= 1
        
class TunnelThread (threading.Thread):
    def __init__(self, ind, name, sport, dsthost, dstport, user, password, server, server_port):
        threading.Thread.__init__(self)
        self.ind = ind
        self.name = name
        self.sport = str(sport)
        self.dsthost = dsthost
        self.dstport = str(dstport)
        self.user = user
        self.password = password
        self.server = server
        self.server_port = str(server_port)
        
    def run(self):
        global stop_tunnel
        print("Starting Tunnel " + self.name)
        while stop_tunnel == False:
            os.system('sshpass -p '+self.password+' ssh -R '+self.sport+':'+self.dsthost+':'+self.dstport+' '+self.user+'@'+self.server+' -p '+self.server_port+' -N -o UserKnownHostsFile=/dev/null -o StrictHostKeyChecking=no -o ServerAliveInterval=15 -o ServerAliveCountMax=3')
            if stop_tunnel == False:
                print("Tunnel "+self.name+" uscito anticipatamente")
            time.sleep(1)
        print("End Tunnel " + self.name)


def SerialNumber(cfg_data):
    macbin = get_mac()
    sn = ''.join('%02X' % ((macbin >> 8*i) & 0xff) for i in reversed(range(6)))
    cfg_data['sn'] = sn
    f = open(cfg_path+'.tmp', 'w')
    cfg_json = json.dumps(cfg_data)
    f.write(cfg_json)
    f.close()
    os.rename(cfg_path+'.tmp', cfg_path)
    os.system('chown www-data:www-data '+cfg_path)
    return sn
    
        
def ServerRegistration(cfg_data, appl):
    if 'sn' in cfg_data and cfg_data['sn'] != '':
        sn = cfg_data['sn']
    else:
        sn = SerialNumber(cfg_data)
    ck = hashlib.md5()
    ck.update((sn+appl['name']+'/'+appl['version']).encode('utf-8'))
    ck = ck.hexdigest()
    idn = ''
    enckey = ''
    try:
        if cfg_data['scheme'] == 'http':
            conn = http.client.HTTPConnection(cfg_data['host'])
        else:
            conn = http.client.HTTPSConnection(cfg_data['host'])
        conn.request('GET', cfg_data['path']+'?sn='+sn+'&ck='+ck, headers={'User-Agent': appl['name']+'/'+appl['version']})
        resp = conn.getresponse()
        body = resp.read()
        fcmd = tmp_dir+'/cmd.json'
        f = open(fcmd+'.cpt', 'wb')
        f.write(body)
        f.close()
        if os.path.isfile(fcmd):
            os.remove(fcmd)
        os.system('/usr/bin/ccrypt -f -d -K '+sn+' '+fcmd+'.cpt')
        #os.remove(fcmd+'.cpt')
        f = open(fcmd)
        action = json.load(f)
        f.close()
        os.remove(fcmd)
        if 'idn' in action:
            if 'enckey' in action:
                idn = action['idn']
                enckey = action['enckey']
                print('Chiave: '+enckey)
    except Exception as e:
        print(Exception('Registration Error: %s' % e))
    finally:
        conn.close()
    if idn != '':
        f = open(cfg_path+'.tmp', 'w')
        cfg_json = '{"scheme": "'+cfg_data['scheme']+'", "host": "'+cfg_data['host']+'", "path": "'+cfg_data['path']+'", "idn": "'+idn+'", "sn": "'+sn+'", "enckey": "'+enckey+'"}'
        f.write(cfg_json)
        f.close()
        os.rename(cfg_path+'.tmp', cfg_path)


def StartTunnels(data):
    i = 0
    for tun in data['tunnels']:
        # Create new thread
        thread = TunnelThread(i, tun['name'], tun['sport'], tun['dsthost'], tun['dstport'], data['params']['user'], data['params']['password'], data['params']['server'], data['params']['sshport'])
        # Start new Thread
        thread.daemon = True
        thread.start()
        # Add threads to thread list
        threads.append(thread)
        i += 1


def ForseRegist(cfg_data):
    f = open(cfg_path+'.tmp', 'w')
    cfg_json = '{"scheme": "'+cfg_data['scheme']+'", "host": "'+cfg_data['host']+'", "path": "'+cfg_data['path']+'", "sn": "'+cfg_data['sn']+'"}'
    f.write(cfg_json)
    f.close()
    os.rename(cfg_path+'.tmp', cfg_path)

    
def MngAction(cmd, cfg_data):
    global stop_tunnel, tunnel_state
    if cmd['action'] == 'start':
        stop_tunnel = False
        StartTunnels(cmd)
        tunnel_state = 'on'
        print('Tunnels started')
        
    elif cmd['action'] == 'stop':
        stop_tunnel = True
        os.system('killall ssh')
        for t in threads:
            t.join()
        tunnel_state = 'off'
        print("Tunnels stopped")
        
    elif cmd['action'] == 'register':
        ForseRegist(cfg_data)
    
def main():
    global tunnel_state
    # timeout errori in connessione
    error_cnt = 0
    # dati applicazione
    f = open('app.json')
    appl = json.load(f)
    f.close()
    cfg_data = None
    next_call = 5
    
    # registrazione al server... se non gia' eseguita
    while True:
        idn = False
        new_act = False
        while idn == False:
            cfg = False
            while cfg == False:
                try:
                    if not os.path.isfile(cfg_path):
                        SerialNumber({})
                    f = open(cfg_path)
                    cfg_data = json.load(f)
                    f.close()
                    if 'sn' not in cfg_data:
                        SerialNumber(cfg_data)
                    if 'host' in cfg_data:
                        cfg = True
                except:
                    pass
                time.sleep(0.25)
            if 'idn' not in cfg_data:
                error_cnt = 0
                time.sleep(1)
                print('Registrazione')
                ServerRegistration(cfg_data, appl)
            else:
                idn = True
            
        # richiesta al server apertura/chiusura tunnel
        try:
            print('Server CMD')
            if cfg_data['scheme'] == 'http':
                conn = http.client.HTTPConnection(cfg_data['host'])
            else:
                conn = http.client.HTTPSConnection(cfg_data['host'])
            conn.request('GET', cfg_data['path']+'?idn='+cfg_data['idn']+'&st='+tunnel_state, headers={'User-Agent': appl['name']+'/'+appl['version']})
            resp = conn.getresponse()
            body = resp.read()
            f = open(tmp_dir+'/cmd.json.cpt', 'wb')
            f.write(body)
            f.close()
            os.system('/usr/bin/ccrypt -f -d -K '+cfg_data['enckey']+' '+tmp_dir+'/cmd.json.cpt')
            f = open(tmp_dir+'/cmd.json')
            action = json.load(f)
            f.close()
            os.remove(tmp_dir+'/cmd.json')
            print(action)
            if 'action' in action:
                new_act = True
            if 'next_call' in action:
                next_call = action['next_call']
        except Exception as e:
            error_cnt += 1
            print(Exception('Connection Error: %s' % e))
        finally:
            conn.close()
            
        if new_act:
            MngAction(action, cfg_data)
        elif error_cnt >= 5:
            print('Rinegoziazione')
            ForseRegist(cfg_data)
        
        time.sleep(next_call)
    
    
if __name__ == '__main__':
    main()
