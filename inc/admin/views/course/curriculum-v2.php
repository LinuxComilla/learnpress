<?php
/**
 * Template curriculum course.
 *
 * @since 3.0.0
 */

learn_press_admin_view( 'course/sections' );
?>
<script type="text/x-template" id="tmpl-lp-course-curriculum">
    <div id="lp-course-curriculum" class="lp-course-curriculum">
        <div class="heading">
            <h4><?php _e( 'Curriculum', 'learnpress' ); ?></h4>
            <p class="description"><?php _e( 'Outline your course and add content with sections, lessons and quizzes.', 'learnpress' ); ?></p>
        </div>

        <lp-list-sections></lp-list-sections>
    </div>
</script>

<script>
    (function (Vue, $store) {

        Vue.component('lp-curriculum', {
            template: '#tmpl-lp-course-curriculum'
        });

    })(Vue, LP_Curriculum_Store);
</script>