var gulp = require('gulp');
var sass = require('gulp-sass');
var minifyCSS = require('gulp-csso');
var concat = require('gulp-concat');


var adminJS = [
    './public/bundles/trendixadmin/js/conditional_fields.js',
    './public/bundles/trendixadmin/js/formUtils.js',
    './public/bundles/trendixadmin/js/inc/actions.js',
    './public/bundles/trendixadmin/js/inc/paginator.js',
    './public/bundles/trendixadmin/js/utils.js',
    './public/bundles/trendixadmin/pickadate/lib/picker.js',
    './public/bundles/trendixadmin/pickadate/lib/picker.date.js',
    './public/bundles/trendixadmin/pickadate/lib/picker.time.js',
    './public/bundles/trendixadmin/jcrop/js/jquery.color.js',
    './public/bundles/trendixadmin/jcrop/js/jquery.fileupload.js',
    './public/bundles/trendixadmin/jcrop/js/jquery.Jcrop.min.js',
    './public/bundles/trendixadmin/jcrop/js/imageWidget.js'
];

var bowerDependencies = [
    './bower_components/flot/jquery.flot.js',
    './bower_components/flot/jquery.flot.resize.js',
    './bower_components/flot.curvedlines/curvedLines.js',
    './bower_components/jquery.easy-pie-chart/dist/jquery.easypiechart.min.js',
    './bower_components/moment/min/moment.min.js',
    './bower_components/fullcalendar/dist/fullcalendar.min.js',
    './bower_components/sweetalert2/dist/sweetalert2.all.min.js',
    './bower_components/jquery.scrollbar/jquery.scrollbar.min.js',
    './bower_components/autosize/dist/autosize.min.js',
];

var appJS = [
    './bower_components/jquery/dist/jquery.min.js',
    './bower_components/bootstrap/dist/js/bootstrap.min.js',
    './bower_components/popper.js/dist/umd/popper.min.js',
    './bower_components/inputmask/dist/min/jquery.inputmask.bundle.min.js',
    './bower_components/pickadate/lib/picker.js',
    './bower_components/pickadate/lib/picker.date.js',
    './bower_components/pickadate/lib/translations/es_ES.js',
    './bower_components/scrollreveal/dist/scrollreveal.js'
];

var headerAppJS = [
    './bower_components/font-awesome/js/all.min.js'
];

var headerJS = [];

var frontSass = [
    './bower_components/pickadate/lib/themes/default.css',
    './bower_components/pickadate/lib/themes/default.date.css',
    './bower_components/font-awesome/css/all.css',
    './public/scss/front.scss'
];

function waitForStream(stream) {
    return new Promise(function(resolve, reject) {
        stream.once('error', reject);
        stream.once('finish', resolve);
    });
}

function compileSass() {
    return Promise.all([
        waitForStream(gulp.src('./public/bundles/*/**/app.scss')
            .pipe(sass())
            .pipe(concat('app.css'))
            .pipe(minifyCSS())
            .pipe(gulp.dest('./public/css'))),
        waitForStream(gulp.src([
            './public/bundles/trendixadmin/fonts/*',
            './public/bundles/trendixadmin/fonts/**/*'
        ]).pipe(gulp.dest('./public/fonts')))
    ]);
}

function compileFrontSass() {
    return Promise.all([
        waitForStream(gulp.src(frontSass)
            .pipe(sass())
            .pipe(concat('front.css'))
            .pipe(minifyCSS())
            .pipe(gulp.dest('./public/css'))),
        waitForStream(gulp.src('./bower_components/font-awesome/webfonts/**/*')
            .pipe(gulp.dest('./public/webfonts')))
    ]);
}

gulp.task('sass', function() {
    return Promise.all([compileSass(), compileFrontSass()]);
});

function compileJS() {
    return Promise.all([
        waitForStream(gulp.src(headerJS.concat([
        './bower_components/popper.js/dist/umd/popper.min.js',
        './bower_components/jquery/dist/jquery.min.js',
        './bower_components/bootstrap/dist/js/bootstrap.min.js'
        ]))
            .pipe(concat('header.js'))
            .pipe(gulp.dest('./public/js'))),

        waitForStream(gulp.src(headerAppJS)
            .pipe(concat('header-app.js'))
            .pipe(gulp.dest('./public/js'))),

        waitForStream(gulp.src(bowerDependencies.concat(adminJS))
            .pipe(concat('footer.js'))
            .pipe(gulp.dest('./public/js'))),

        waitForStream(gulp.src(appJS)
            .pipe(concat('app.js'))
            .pipe(gulp.dest('./public/js')))
    ]);
}
gulp.task('js', function() {
    return compileJS();
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
