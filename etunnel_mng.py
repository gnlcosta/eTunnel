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
import logging

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
            os.system('sshpass -p '+self.password+' ssh -R '+self.sport+':'+self.dsthost+':'+self.dstport+' '+self.user+'@'+self.server+' -p '+self.server_port+' -N -o UserKnownHostsFile=/dev/null -o StrictHostKeyChecking=no -o ServerAliveInterval=10 -o ServerAliveCountMax=3')
            if stop_tunnel == False:
                print("Tunnel "+self.name+" uscito anticipatamente")
            time.sleep(1)
        print("End Tunnel " + self.name)


def FireWall(enable):
    print('Firewall functionality disabled')
    return
    # network interfaces
    netlist = []
    
    try:
        for (dirpath, dirnames, filenames) in os.walk('/sys/class/net/'):
            for dirp in dirnames:
                #if os.path.isfile('/sys/class/net/'+dirp+'/')
                if os.path.isdir('/sys/class/net/'+dirp+'/device'):
                    if os.path.isdir('/sys/class/net/'+dirp+'/wireless'):
                        netlist.append(dirp)
                    elif 'usb' not in dirp:
                        netlist.append(dirp)
    except:
        return
    
    if enable == True and len(netlist) > 0:
        print('Firewall ON')
        os.system('iptables -F')
        os.system('iptables -X')
        os.system('iptables -P INPUT ACCEPT')
        os.system('iptables -P FORWARD DROP')
        os.system('iptables -P OUTPUT ACCEPT')
        os.system('iptables -A INPUT -m state --state ESTABLISHED,RELATED -j ACCEPT')
        # accept on localhost
        os.system('iptables -A INPUT -i lo -j ACCEPT')
        os.system('iptables -A OUTPUT -o lo -j ACCEPT')
        for intf in netlist:
            print('Firewall '+intf+' ON')
            os.system('iptables -A INPUT -i '+intf+' -j ACCEPT')
            os.system('iptables -A OUTPUT -o '+intf+' -j ACCEPT')
        # drop all other incoming connections
        os.system('iptables -A INPUT -j DROP')
    else:
        print('Firewall OFF')
        os.system('iptables -F')
        os.system('iptables -t nat -F')
        os.system('iptables -t mangle -F')
        os.system('iptables -X')
        os.system('iptables -t nat -X')
        os.system('iptables -t mangle -X')
        os.system('iptables -P INPUT ACCEPT')
        os.system('iptables -P FORWARD ACCEPT')
        os.system('iptables -P OUTPUT ACCEPT')


def SerialNumber():
    macbin = get_mac()
    sn = ''.join('%02X' % ((macbin >> 8*i) & 0xff) for i in reversed(range(6)))
    return sn


def SerialNumberSave(cfg_data):
    sn = SerialNumber()
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
        sn = SerialNumberSave(cfg_data)
    ck = hashlib.md5()
    ck.update((sn+appl['name']+'/'+appl['version']).encode('utf-8'))
    ck = ck.hexdigest()
    idn = ''
    enckey = ''
    try:
        if cfg_data['scheme'] == 'http':
            conn = http.client.HTTPConnection(cfg_data['host'], timeout=9)
        else:
            conn = http.client.HTTPSConnection(cfg_data['host'], timeout=9)
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
        os.system('chown www-data:www-data '+cfg_path)
        


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


def ReqNewRegist(cfg_data):
    sn = SerialNumber()
    f = open(cfg_path+'.tmp', 'w')
    cfg_json = '{"scheme": "'+cfg_data['scheme']+'", "host": "'+cfg_data['host']+'", "path": "'+cfg_data['path']+'", "sn": "'+sn+'"}'
    f.write(cfg_json)
    f.close()
    os.rename(cfg_path+'.tmp', cfg_path)
    os.system('chown www-data:www-data '+cfg_path)

    
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
        ReqNewRegist(cfg_data)

    
def main():
    global tunnel_state
    logging.basicConfig(filename='/data/embed/etunnel/etunnel.log', format='%(asctime)s %(levelname)s:%(message)s', level=logging.WARNING)
    
    # timeout errori in connessione
    error_cnt = 0
    # dati applicazione
    f = open('app.json')
    appl = json.load(f)
    f.close()
    cfg_data = None
    timeout_stop = -1
    firewall = False
    
    # registrazione al server... se non gia' eseguita
    while True:
        next_call = 5 # valore di default (in caso di errore in connesisone...)
        idn = False
        new_act = False
        while idn == False:
            cfg = False
            while cfg == False:
                cfg = True
                try:
                    if not os.path.isfile(cfg_path):
                        SerialNumberSave({})
                    f = open(cfg_path)
                    cfg_data = json.load(f)
                    f.close()
                    if 'sn' not in cfg_data:
                        SerialNumberSave(cfg_data)
                        cfg = False
                    if cfg_data['sn'] != SerialNumber():
                        cfg_data['idn'] = ''
                        del cfg_data['idn']
                        SerialNumberSave(cfg_data)
                        cfg = False
                    if 'host' not in cfg_data:
                        cfg = False
                except:
                    cfg = False
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
            # print('Server CMD')
            if cfg_data['scheme'] == 'http':
                conn = http.client.HTTPConnection(cfg_data['host'], timeout=9)
            else:
                conn = http.client.HTTPSConnection(cfg_data['host'], timeout=9)
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
            # print(action)
            if 'action' in action:
                new_act = True
            if 'next_call' in action:
                next_call = action['next_call']
            if firewall == False:
                firewall = True
                FireWall(True)
        except Exception as e:
            error_cnt += 1
            logging.debug('Connection Error: %s', e)
            if 'resp' in locals():
                if hasattr(resp, 'status') and hasattr(resp, 'reason'):
                    logging.debug(' Con: %s %s', resp.status, resp.reason)
            print(Exception('Connection Error: %s' % e))
        finally:
            conn.close()
            
        if new_act:
            MngAction(action, cfg_data)
            if 'params' in action and 'timeout' in action['params']:
                timeout_stop = time.time() + action['params']['timeout']
            else:
                timeout_stop = -1
        elif error_cnt >= 5:
            print('Rinegoziazione')
            ReqNewRegist(cfg_data)
            firewall = False
            FireWall(False)

        i = 0
        while i < next_call:
            # chiusura per timeout
            if timeout_stop != -1 and timeout_stop < time.time():
                MngAction({'action': 'stop'}, cfg_data)
                timeout_stop = -1
            time.sleep(1)
            i += 1
    
if __name__ == '__main__':
    main()
