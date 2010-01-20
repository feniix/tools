import os
import urllib2
from BeautifulSoup import BeautifulSoup


basedir = '/home/otaeguis/src/JBPAPP_4_3_0_GA/thirdparty'
infofile = 'component-info.xml'
tablesep = '|'

for dir in os.listdir(basedir):
    filepath = ''.join([basedir, '/', dir, '/', infofile])
    if os.path.exists(filepath):
        filepath = ''.join(['file://',filepath])
        file = urllib2.urlopen(filepath)
    else:
        continue
    
    xml = file.read()
    soup = BeautifulSoup(xml)
    
    for attr, value in soup.find('artifact').attrs:
        if attr == 'id':
            table = tablesep + value + tablesep
    for attr, value in soup.find('component').attrs:
        if attr == 'version':
            table += value + tablesep
        if attr == 'description':
            table += value + tablesep
    print table




