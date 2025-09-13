#!/bin/bash
# Supervisord takes over from here 
/usr/bin/supervisord -c /deeptune/supervisord.conf -n
