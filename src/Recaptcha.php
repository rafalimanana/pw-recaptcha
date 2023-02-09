<?php 

namespace Pw\Recaptcha;

class Recaptcha
{

    /**
     * Class constructor
     *
     * @param array $options
     */
    public function __construct($options = [])
    {
    	$secret = null;
    	$recaptcha_key = null;
    	$recaptcha_score_threshold = null;

    	if (!is_array($options)) {
    		$options = [];
    	}

    	if ($options && isset($options["secret"])) {
    		$secret = $options["secret"];
    	}
    	if ($options && isset($options["recaptcha_key"])) {
    		$recaptcha_key = $options["recaptcha_key"];
    	}
    	if ($options && isset($options["recaptcha_score_threshold"])) {
    		$recaptcha_score_threshold = $options["recaptcha_score_threshold"];
    	}
        $this->secret = $secret;
        $this->recaptcha_key = $recaptcha_key;
        $this->recaptcha_score_threshold = $recaptcha_score_threshold;
    }

    /**
     * @param $recaptcha_token
     * @return boolean
     */
    public function isValidReCaptcha(
    	$recaptcha_token,
        &$recaptcha=null
    ){
        if(
        	!$this->secret || 
        	!$recaptcha_token || 
        	!$this->recaptcha_score_threshold
        ){
            return false;
        }

        $recaptcha = $this->checkReCaptcha(
        	$recaptcha_token,
        	$this->secret
        );

        return (
            $recaptcha &&
            isset($recaptcha['success']) && $recaptcha['success'] &&
            isset($recaptcha['score']) && ($recaptcha['score'] >= $this->recaptcha_score_threshold)
        );
    }

    /**
     * @param $recaptcha_token
     * @param string $secret
     * @return array
     */
    public function checkReCaptcha(
    	$recaptcha_token,
    	$secret
    ) {
        $url = "https://www.google.com/recaptcha/api/siteverify";
        $response = $recaptcha_token;

        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => 'https://www.google.com/recaptcha/api/siteverify',
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => [
                'secret' => $secret,
                'response' => $response,
                'remoteip' => $_SERVER['REMOTE_ADDR']
            ],
            CURLOPT_RETURNTRANSFER => true
        ]);

        $output = curl_exec($ch);
        curl_close($ch);

        $json = json_decode($output,true);

        return $json;
    }
}