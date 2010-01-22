import os
import urllib2
import re
from BeautifulSoup import BeautifulSoup


basedir = '/CHANGEME/temp/thirdparty' # this is the directory that needs to be changed
infofile = 'component-info.xml'
ts = '|'
header = '||Component||Library||Version||Description||Comment||'

print header

for dir in sorted(os.listdir(basedir)):
    if dir == '':
        print 'The directory structure is not adequate'
        break
    filepath = ''.join([basedir, '/', dir, '/', infofile])
    if os.path.exists(filepath):
        filepath = ''.join(['file://',filepath])
        file = urllib2.urlopen(filepath)
    else:
        continue
    
    xml = file.read()
    soup = BeautifulSoup(xml)

    for attr, value in soup.find('component').attrs:
        if attr == 'id':
            c_attr = value
        elif attr == 'description':
            d_attr = str(value).replace('\n', '').replace('             ', '')
        elif attr == 'version':
            v_attr = value
        else:
            continue

    artifacts = soup.findAll('artifact')
    for line in artifacts:
        ids = re.split('"', str(line))
        print ts + c_attr + ts + ids[1] + ts + v_attr + ts + d_attr + ts + " " + ts


