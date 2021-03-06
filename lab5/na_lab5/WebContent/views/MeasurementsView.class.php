<?php 
class MeasurementsView{
    
    public static function show() {
        if (!isset($_SESSION['styles']))
            $_SESSION['styles'] = array();
        if (!isset($_SESSION['scripts']))
            $_SESSION['scripts'] = array();
        if (!isset($_SESSION['libraries']))
            $_SESSION['libraries'] = array();
        $_SESSION['styles'][] = 'MeasurementsStyles.css';
        $_SESSION['libraries'][] = 'highcharts/highcharts.js';
        $_SESSION['scripts'][] = 'MeasurementsScripts.js';
        
        if (isset($_SESSION['profile']) && $_SESSION['profile']->getTheme() === 'dark')
            $_SESSION['libraries'][] = 'highcharts/dark-unica.js';
        HeaderView::show("Your Past Measurements");
        MeasurementsView::showBody();
        FooterView::show();
    }
    
    public static function showBody() {
        if (!isset($_SESSION) || !isset($_SESSION['measurements'])):
            ?><p>Error: unable to show measurements. Data is missing.</p><?php
            return;
        endif;
        $measurements = $_SESSION['measurements'];
        ?>

<div id="page-nav" class="row">
    <div class="col-md-12">
        <div class="row">
            <div class="col-sm-12">
                <p class="buttonGroupLabel">Click to jump to a measurement:</p>
            </div>
        </div>
        <div class="row">
            <div class="jump-to col-xs-4 col-sm-2">
                <button type="button" name="glucose" class="btn btn-default btn-block">Glucose</button>
            </div>
            <div class="jump-to col-xs-4 col-sm-2">
                <button type="button" name="bloodPressure" class="btn btn-default btn-block">Blood Pressure</button>
            </div>
            <div class="jump-to col-xs-4 col-sm-2">
                <button type="button" name="calories" class="btn btn-default btn-block">Calories</button>
            </div>
            <div class="jump-to col-xs-4 col-sm-2">
                <button type="button" name="exercise" class="btn btn-default btn-block">Exercise</button>
            </div>
            <div class="jump-to col-xs-4 col-sm-2">
                <button type="button" name="sleep" class="btn btn-default btn-block">Sleep</button>
            </div>
            <div class="jump-to col-xs-4 col-sm-2">
                <button type="button" name="weight" class="btn btn-default btn-block">Weight</button>
            </div>
        </div>
        <hr />
    </div>
</div>

<section id="glucose" class="row">
    <div class="col-sm-12">
        <?php GlucoseMeasurementsView::showBody(); ?>
    </div>
</section>

<section id="bloodPressure" class="row">
    <div class="col-sm-12">
        <?php BloodPressureMeasurementsView::showBody(); ?>
    </div>
</section>

<section id="calories" class="row">
    <div class="col-sm-12">
        <?php CalorieMeasurementsView::showBody(); ?>
    </div>
</section>

<section id="exercise" class="row">
    <div class="col-sm-12">
        <?php ExerciseMeasurementsView::showBody(); ?>
    </div>
</section>

<section id="sleep" class="row">
    <div class="col-sm-12">
        <?php SleepMeasurementsView::showBody(); ?>
    </div>
</section>

<section id="weight" class="row">
    <div class="col-sm-12">
        <?php WeightMeasurementsView::showBody(); ?>
    </div>
</section>
        
<?php
        if (isset($_SESSION)) {
            unset($_SESSION['measurements']);
            unset($_SESSION['styles']);
            unset($_SESSION['scripts']);
        }
    }
    
}