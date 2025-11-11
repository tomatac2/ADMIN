<?php

namespace App\Helpers;

class Response
{
    public const SUCCESS = 'response.success';
    public const FAILED = 'response.failed';
    public const EMPTYDATA = 'response.empty_data';
    public const NOT_AUTHORIZED = 'response.not_authorized';
    public const NOT_AUTHENTICATED = 'response.not_authenticated';
    public const USER_NOT_FOUND = 'response.user_not_found';
    public const NOT_VERIFIED = 'response.not_verified';
    public const NOT_ENABLED = 'response.not_enabled';
    public const NOT_CORRECT_OTP = 'response.Your_Otp_is_not_correct';
    public const DELETE_REQUEST_SUCCESSFULLY = 'response.deleted_request_successfully';
    public const EMAIL_ALREADY_BOOKED = 'response.requested_mail_already_booked_by_anther_user';
    public const MOBILE_ALREADY_BOOKED = 'response.requested_mobile_already_booked_by_anther_user';
    public const STATUS_SWITCHED_SUCCESSFULLY = 'response.status_switched_successfully';
    public const YOUR_REQUEST_UNDER_REVIEW = 'response.your_request_is_under_review';
    public const MESSAGE_SENDED_SUCCESSFULLY = 'response.message_sended_successfully';
    public const ORDER_STATUS_CHANGED_SUCCESSFULLYS = 'response.order_status_changed_successfully';
    public const WITIHDROW_REQUEST_SUCCESSFULLY = 'response.withdrow_request_successfully';

    public const WRONG_PASSWORD = 'response.wrong_password';
    public const LOGIN_SUCCESSFULLY = 'response.login_successfully';
    public const REGISTER_SUCCESSFULLY = 'response.register_successfully';
    public const REGISTER_OTP_SUCCESSFULLY = 'response.register_otp_successfully';
    public const OTP_SEND_SUCCESSFULLY = 'response.otp_send_successfully';
    public const PROFILE_CHANGE_INFO_REQUEST = 'response.profile_cahnge_info_successfully';

    public const REQUEST_UNDER_REVIEW = 'response.request_under_review';

    public const LOGOUT_SUCCESSFULLY = 'response.logout_successfully';
    public const LOGIN_FAILED = 'response.login_failed';
    public const REGISTER_FAILED = 'response.register_failed';
    public const CODE_FAILED = 'response.code_failed';
    public const MUST_LOGIN = 'response.must_login';

    public const ADDED_SUCCESSFULLY = 'response.added_successfully';
    public const UPDATED_SUCCESSFULLY = 'response.updated_successfully';
    public const CONFIRMED_SUCCESSFULLY = 'response.confirmed_successfully';

    public const COUNTRY_ADDED_SUCCESSFULLY = 'response.country_added_successfully';
    public const COUNTRY_UPDATED_SUCCESSFULLY = 'response.country_updated_successfully';
    public const REGION_ADDED_SUCCESSFULLY = 'response.region_added_successfully';
    public const REGION_UPDATED_SUCCESSFULLY = 'response.region_updated_successfully';

    public const CITY_ADDED_SUCCESSFULLY = 'response.city_added_successfully';
    public const CITY_UPDATED_SUCCESSFULLY = 'response.city_updated_successfully';
    public const USER_ADDED_SUCCESSFULLY = 'response.user_added_successfully';
    public const USER_UPDATED_SUCCESSFULLY = 'response.user_updated_successfully';
    public const OWNER_ADDED_SUCCESSFULLY = 'response.owner_added_successfully';
    public const OWNER_UPDATED_SUCCESSFULLY = 'response.owner_updated_successfully';
    public const COMPANY_ADDED_SUCCESSFULLY = 'response.company_added_successfully';
    public const COMPANY_UPDATED_SUCCESSFULLY = 'response.company_updated_successfully';
    public const ROLE_ADDED_SUCCESSFULLY = 'response.role_added_successfully';
    public const ROLE_UPDATED_SUCCESSFULLY = 'response.role_updated_successfully';

    public const COUNTRY_DELETED_SUCCESSFULLY = 'response.country_deleted_successfully';
    public const CITY_DELETED_SUCCESSFULLY = 'response.city_deleted_successfully';
    public const USER_DELETED_SUCCESSFULLY = 'response.user_deleted_successfully';
    public const OWNER_DELETED_SUCCESSFULLY = 'response.owner_deleted_successfully';
    public const COMPANY_DELETED_SUCCESSFULLY = 'response.company_deleted_successfully';
    public const ROLE_DELETED_SUCCESSFULLY = 'response.role_deleted_successfully';

    public const DELETED_SUCCESSFULLY = 'response.deleted_successfully';
    public const TRASHED_SUCCESSFULLY = 'response.trashed_successfully';
    public const RESTORED_SUCCESSFULLY = 'response.restored_successfully';
    public const NOT_ALLOWED = 'response.not_allowed';
    public const NOT_FOUND = 'response.not_found';
    public const NO_REGION_PRICE_HERE = 'response.no_region_price_here';
    public const WRONG_ANS_NUMBER = 'response.wrong_answers_numbers';
    public const EXCEEDED_CAMPAIGN_END_DATE = 'validation.custom.exceeded_campaign_end_date';
    public const EDITED = 'response.edited';
    public const AUDIENCE_ROLE_ALREADY_ASSIGNED = 'response.audience_role_already_assigned';
    public const FEEDBACK_WAS_SENT = 'response.feedback_was_sent';
    public const PROGRAM_PRESENTATION_NOT_FINISHED_YET = 'response.prgram_presentaion_not_finished_yet';
    public const REQUEST_WILL_PROCESSING = 'response.your_request_will_processing';

    public const DURATION_OUT_OF_TIME_LINE = 'response.duration_out_of_time_line';

    public const INTEGRATION_CONNECTION_FAILED = 'response.integration_connection_failed';

    public const CHECK_PHISH_PROCESSING = 'response.your_request_to_check_the_phishing_link_will_processing';
    public const IMPORT_USERS_PROCESSING = 'response.your_request_import_users_will_processing';
    public const IMPORT_USERS_FAILED = 'response.your_request_import_users_failed';

    public const LDAP_CONNECTION_SUCCESS = 'response.ldap_connection_success';
    public const LDAP_CONNECTION_FAILD = 'response.ldap_connection_failed';
    public const AZUR_CONNECTION_SUCCESS = 'response.azur_connection_success';
    public const AZUR_CONNECTION_FAILD = 'response.azur_connection_failed';
    public const CENTER_X_CONNECTION_FAILED = 'response.center_x_connection_failed';

    public const VERIFIED = 'response.verified';
    public const USER_EXISTS = 'response.user_exists';

    public const OTP_EXPIRED = 'response.Your_OTP_has_been_expired';
    public const RESEND_OTP_SUCCESSFULLY = 'response.resend_code_successfully';

    public const ADDRESS_RATED_SUCCESSFULLY = 'response.address_rated_successfully';

    public const OTP_SENDED_SUCCESSFULLY = 'response.otp_sended_successfully';

    /**
     * @param mixed $message
     * @param null $content
     * @param integer $status
     *
     * @return JsonResponse
     */
    public static function respondSuccess($message, $content = null, $status = 200)
    {
        return response()->json(
            [
                'status' => true,
                'message' => __($message),
                'data' => $content,
            ],
            $status,
        );
    }

    /**
     * @param mixed $message
     * @param integer $status
     *
     * @return JsonResponse
     */
    public static function respondError($message, $status = 500, $data = null)
    {
        return response()->json(
            [
                'status' => false,
                'message' => __($message),
                'data' => $data,
            ],
            $status,
        );
    }

    public static function errorResponse($message, $code = 422)
    {
        $array = [
            'status' => false,
            'message' => $message,
            'data' => null,
        ];
        return response($array, $code);
    }

    public static function respondSuccessPaginate($message, $content = null, $status = 200)
    {
        $response = [
            'status' => true,
            'message' => __($message),
        ];

        if (is_array($content) && isset($content['data']) && isset($content['pagination'])) {
            $response['data'] = $content['data'];
            $response['pagination'] = $content['pagination'];
        } else {
            $response['data'] = $content;
        }

        return response()->json($response, $status);
    }
}
