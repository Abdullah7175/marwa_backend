module.exports = {
  apps: [{
    name: 'marwah-seo-backend',
    script: 'artisan',
    args: 'serve --host=0.0.0.0 --port=8000',
    interpreter: 'php',
    cwd: '/var/www/marwah-travels',
    instances: 1,
    autorestart: true,
    watch: false,
    max_memory_restart: '1G',
    env: {
      NODE_ENV: 'production'
    },
    error_file: '/var/log/pm2/marwah-travels-error.log',
    out_file: '/var/log/pm2/marwah-travels-out.log',
    log_file: '/var/log/pm2/marwah-travels.log',
    time: true
  }]
};
