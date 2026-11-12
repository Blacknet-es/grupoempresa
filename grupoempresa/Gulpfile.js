var gulp = require('gulp');
var sass = require('gulp-sass');
var util = require('gulp-util');
var minifyCSS = require('gulp-csso');
var uglify = require('gulp-uglify');
var concat = require('gulp-concat');


var adminJS = [
    './public/bundles/trendixadmin/vendors/sparklines/jquery.sparkline.min.js',
    './public/bundles/trendixadmin/vendors/bootstrap-growl/bootstrap-growl.min.js',
    './public/bundles/trendixadmin/vendors/summernote/dist/summernote-updated.min.js',
    './public/bundles/trendixadmin/js/charts.js',
    './public/bundles/trendixadmin/js/functions.js',
    './public/bundles/trendixadmin/js/paginator.js',
    './public/bundles/trendixadmin/pickadate/lib/picker.js',
    './public/bundles/trendixadmin/pickadate/lib/picker.date.js',
    './public/bundles/trendixadmin/pickadate/lib/picker.time.js',
    './public/bundles/trendixadmin/js/datepicker.js',
    './public/bundles/trendixadmin/js/jscolor.min.js',
    './public/bundles/trendixadmin/js/image_uploader.js',
    './public/bundles/trendixadmin/js/conditional_fields.js',
    './public/bundles/trendixadmin/js/actions.js',
    './public/bundles/trendixadmin/jcrop/js/jquery.color.js',
    './public/bundles/trendixadmin/jcrop/js/jquery.fileupload.js',
    './public/bundles/trendixadmin/jcrop/js/jquery.Jcrop.min.js',
    './public/bundles/trendixadmin/jcrop/js/imageWidget.js',
    './public/bundles/trendixadmin/js/utils.js',
    './public/bundles/trendixadmin/js/formUtils.js'
];

var bowerDependencies = [
    './bower_components/flot/jquery.flot.js',
    './bower_components/flot/jquery.flot.resize.js',
    './bower_components/flot.curvedLines/curvedLines.js',
    './bower_components/jquery.easy-pie-chart/dist/jquery.easypiechart.min.js',
    './bower_components/moment/min/moment.min.js',
    './bower_components/fullcalendar/dist/fullcalendar.min.js',
    './bower_components/simpleWeather/jquery.simpleWeather.min.js',
    './bower_components/Waves/dist/waves.min.js',
    './bower_components/bootstrap-sweetalert/lib/sweet-alert.min.js',
    './bower_components/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.concat.min.js',
    './bower_components/autosize/dist/autosize.min.js',
];

var appJS = [
    './bower_components/jquery/dist/jquery.js',
    './public/bootstrap/dist/js/bootstrap.js',
    './bower_components/popper.js/dist/umd/popper.min.js',
    './bower_components/inputmask/dist/min/jquery.inputmask.bundle.min.js',
    './public/bootstrap/dist/js/bootstrap.js',
    './bower_components/pickadate/lib/picker.js',
    './bower_components/pickadate/lib/picker.date.js',
    './bower_components/pickadate/lib/translations/es_ES.js',
    './bower_components/scrollreveal/dist/scrollreveal.js',
    './src/Resources/public/js/paralax.js',
    './src/Resources/public/js/scroll.js'
];

var headerAppJS = [
    './bower_components/font-awesome/svg-with-js/js/fontawesome-all.js'
];

var headerJS = [];

var frontSass = [
    './bower_components/pickadate/lib/themes/default.css',
    './bower_components/pickadate/lib/themes/default.date.css',
    './bower_components/font-awesome/css/all.css',
    './public/scss/front.scss'
];

function compileSass() {
    gulp.src([
        './public/bundles/*/**/app.scss'
    ])
        .pipe(sass().on('error', util.log))
        .pipe(concat('app.css'))
        .pipe(minifyCSS())
        .pipe(gulp.dest('./public/css'));
    gulp.src([
        './public/bundles/trendixadmin/fonts/*',
        './public/bundles/trendixadmin/fonts/**/*'
    ])
        .pipe(gulp.dest('./public/fonts'));
}

function compileFrontSass() {
    gulp.src(frontSass)
        .pipe(sass().on('error', util.log))
        .pipe(concat('front.css'))
        .pipe(minifyCSS())
        .pipe(gulp.dest('./public/css'));
}

gulp.task('sass', function() {
    compileSass();
    //compileFrontSass();
});

function compileJS() {
    gulp.src(headerJS.concat([
        './bower_components/popper.js/dist/umd/popper.min.js',
        './bower_components/jquery/dist/jquery.js',
        './bower_components/bootstrap/dist/js/bootstrap.js'
    ]))
        .pipe(concat('header.js'))
        .pipe(uglify())
        .pipe(gulp.dest('./public/js'));

    gulp.src(headerAppJS)
        .pipe(concat('header-app.js'))
        .pipe(uglify())
        .pipe(gulp.dest('./public/js'));

    gulp.src(bowerDependencies.concat(adminJS))
        .pipe(concat('footer.js'))
        .pipe(uglify())
        .pipe(gulp.dest('./public/js'));

    if(appJS.length) {
        gulp.src(appJS)
            .pipe(concat('app.js'))
            .pipe(uglify())
            .pipe(gulp.dest('./public/js'));
    }
}
gulp.task('js', function() {
    compileJS();
});

gulp.task('watch', function () {
    var onChange = function (event) {
        console.log('File '+event.path+' has been '+event.type);
    };
    gulp.watch('./public/*/**/*.scss', ['sass'])
        .on('change', compileSass);
    gulp.watch('./public/*/**/*.js', ['js'])
        .on('change', compileJS);
});

gulp.task('default', ['sass', 'js']);
