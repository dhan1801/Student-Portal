package com.example.phase3app;

public class ApiClient {

    public static final String BASE_URL = "http://10.0.2.2/database2/phase3/api/endpoints/";

    // Task 1 — Login
    public static final String LOGIN               = BASE_URL + "login_api.php";
    public static final String RESET_PASSWORD      = BASE_URL + "reset_password_api.php";
    public static final String RETRIEVE_LOGIN_INFO = BASE_URL + "retrieve_login_info_api.php";

    // Task 2 — Browse & Register
    public static final String BROWSE_COURSES      = BASE_URL + "browse_courses_api.php";
    public static final String REGISTER_COURSE     = BASE_URL + "register_course_api.php";
    public static final String CURRENT_ENROLLMENTS = BASE_URL + "current_enrollments_api.php";
    public static final String DROP_COURSE         = BASE_URL + "drop_course_api.php";

    // Task 3 — Transcript
    public static final String TRANSCRIPT          = BASE_URL + "transcript_api.php";

    // Task 4 — Discussion Board
    public static final String GET_DISCUSSION         = BASE_URL + "get_discussion_api.php";
    public static final String POST_DISCUSSION        = BASE_URL + "post_discussion_api.php";
    public static final String DELETE_DISCUSSION      = BASE_URL + "delete_discussion_api.php";
    public static final String CHECK_TA_GRADER        = BASE_URL + "check_ta_grader_api.php";
    // Returns enrolled courses PLUS TA/grader courses in one call, with is_ta_or_grader flag
    public static final String DISCUSSION_COURSES     = BASE_URL + "get_discussion_courses_api.php";

    // Task 5 — Course Evaluation
    public static final String EVALUATION_SUBMIT   = BASE_URL + "evaluation_submit_api.php";
}