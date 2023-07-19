# Require any additional compass plugins here.

# 路徑設定
http_path = "/"
css_dir = "css"
sass_dir = "scss"
images_dir = "images"
javascripts_dir = "js"

#sass_options = {:debug_info => true} #debug
output_style = :expanded
#output_style = :compressed #css壓縮設定


# You can select your preferred output style here (can be overridden via the command line):
# output_style = :expanded or :nested or :compact or :compressed

# To enable relative paths to assets via compass helper functions. Uncomment:
# relative_assets = true

# 顯示或去掉sass/scss自動產生的css註解
#line_comments = true
line_comments = false


# If you prefer the indented syntax, you might want to regenerate this
# project again passing --syntax sass, or you can uncomment this:
# preferred_syntax = :sass
# and then run:
# sass-convert -R --from scss --to sass sass scss && rm -rf sass && mv scss sass
preferred_syntax = :scss

# 將絕對路徑更改為相對路徑
relative_assets = true