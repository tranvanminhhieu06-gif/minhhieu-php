#!/bin/bash
set -e

# Determine Port (Render default is 10000)
PORT="${PORT:-10000}"

echo "========================================================="
echo "  👑 HIEU CEO - STARTING APACHE SERVER ON PORT $PORT"
echo "========================================================="

# Update Apache listening port dynamically
echo "Listen $PORT" > /etc/apache2/ports.conf

# Execute Apache in foreground
exec apache2-foreground
