<?php

namespace AppBundle\Controller;


use AppBundle\Entity\User;
use AppBundle\Entity\UserCards;
use AppBundle\Entity\UserPayments;
use AppBundle\Entity\Cars;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityRepository;

use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;

use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\FileParam;
use Acme\FooBundle\Validation\Constraints\MyComplexConstraint;
use FOS\UserBundle\Controller\SecurityController as BaseController;

class UserController extends BaseController
{
    /**
     *
     * HEADER $token
     *
     * @Rest\Get("/landing")
     * @Rest\View
     * @return array
     *
     */
    public function getCars(Request $request){
        return array("success" => true, "msg" => "api de landings");
    }


    /**
     *
     * @REST\Post("/landing/login")
     * @return View
     */
    public function landingLoginAction(Request $request) {

        $data = json_decode($request->getContent(), true);

        $basura = $this->palabrasBasura($data['username']);

        if(!isset($data['username']) || $data['username'] == "" || strlen($data['username']) < 1){
            return array('success' => false, 'msg' => 'Favor de ingresar el usuario', 'input' => 'username');
        }

        if(!isset($data['password']) || $data['password'] == "" || strlen($data['password']) < 1){
            return array('success' => false, 'msg' => 'Favor de ingresar la contraseña', 'input' => 'password');
        }


        return array('success' => true, 'user' => $data['username'], 'password' => $data['password'], 'basura' => $basura);
    }


    /**
     *
     * @REST\Post("/landing/register")
     * @return View
     */
    public function landingRegisterAction(Request $request) {

        $data = json_decode($request->getContent(), true);

        if(!isset($data['canal']) || $data['canal'] == "" || $data['canal'] == '0' || strlen($data['canal']) < 1){
            return array('success' => false, 'msg' => 'Elige un Canal', 'input' => 'canal', 'request'=>$data['canal']);
        }

        if(!isset($data['interes']) || $data['interes'] == "" || $data['interes'] == '0' || strlen($data['interes']) < 1){
            return array('success' => false, 'msg' => 'Elige un ínteres', 'input' => 'interes');
        }

        if(!isset($data['name']) || $data['name'] == ""){
            return array('success' => false, 'msg' => 'Por favor ingresa un nombre', 'input' => 'name');
        }else{
            if(!$this->testName($data['name'])){
                return array('success' => false, 'msg' => 'Nombre inválido', 'input' => 'name');
            }
        }

        if(!isset($data['patern']) || $data['patern'] == "" || strlen($data['patern']) < 1){
            return array('success' => false, 'msg' => 'Por favor ingresa un apellido paterno', 'input' => 'patern');
        }else{
            if(!$this->testName($data['patern'])){
                return array('success' => false, 'msg' => 'Apellido Paterno inválido', 'input' => 'patern');
            }
        }

        if(!isset($data['matern']) || $data['matern'] == "" || strlen($data['matern']) < 1){
            return array('success' => false, 'msg' => 'Por favor ingresa un apellido materno', 'input' => 'matern');
        }else{
            if(!$this->testName($data['matern'])){
                return array('success' => false, 'msg' => 'Apellido Materno inválido', 'input' => 'matern');
            }
        }

        if(!isset($data['mail']) || $data['mail'] == "" || strlen($data['mail']) < 1){
            return array('success' => false, 'msg' => 'Por favor ingresa un correo válido', 'input' => 'mail');
        }else{
            $isFace = substr_count($data['mail'], 'facebook');
            $exp = explode("@",$data['mail']);
            $domain = explode(".",$exp[1]);
            $basura = $this->emailBasura($exp[0]);
            $basuraa = $this->emailBasura($domain[0]);

            if (!filter_var($data['mail'], FILTER_VALIDATE_EMAIL) || $isFace > '0' || $basura || $basuraa) {
                return array('success' => false, 'msg' => 'Correo Inválido', 'input' => 'mail');
            }
        }

        if(!isset($data['cel']) || $data['cel'] == "" || strlen($data['cel']) < 1){
            return array('success' => false, 'msg' => 'Ingresa número de Celular', 'input' => 'cel');
        }else{
            if(strlen($data['cel']) < 10 || !is_numeric($data['cel'])){
                return array('success' => false, 'msg' => 'Al menos debe contener 10 dígitos', 'input' => 'cel');
            }

            $rest = substr($data['cel'], 0, 6);
            $restt = substr($data['cel'], 4, 10);

            $isconsec = $this->numerosConsecutivos($rest);
            $isconsecc = $this->numerosConsecutivos($restt);


            if($isconsec || $isconsecc){
                return array('success' => false, 'msg' => 'Al menos debe contener 10 dígitos.', 'input' => 'cel');
            }
        }

        if(!isset($data['phone']) || $data['phone'] == "" || strlen($data['phone']) < 1){
            return array('success' => false, 'msg' => 'Ingresa número de Teléfono', 'input' => 'phone');
        }else{
            if(strlen($data['phone']) < 10 || !is_numeric($data['phone'])){
                return array('success' => false, 'msg' => 'Al menos debe contener 10 dígitos', 'input' => 'phone');
            }

            $rest = substr($data['phone'], 0, 6);
            $restt = substr($data['phone'], 4, 10);

            $isconsec = $this->numerosConsecutivos($rest);
            $isconsecc = $this->numerosConsecutivos($restt);


            if($isconsec || $isconsecc){
                return array('success' => false, 'msg' => 'Al menos debe contener 10 dígitos.', 'input' => 'phone');
            }
        }

        if(!isset($data['gender']) || $data['gender'] == "" || $data['gender'] == '0' || strlen($data['gender']) < 1){
            return array('success' => false, 'msg' => 'Elige un Género', 'input' => 'gender');
        }

        if(!isset($data['birthday']) || $data['birthday'] == "" || strlen($data['birthday']) < 1){
            return array('success' => false, 'msg' => 'Ingresa una Fecha de Nacimiento', 'input' => 'birthday');
        }

        if(!isset($data['age']) || $data['age'] == "" || strlen($data['age']) < 1){
            return array('success' => false, 'msg' => 'Ingresa una Edad', 'input' => 'age');
        }

        if(!isset($data['interestCampus']) || $data['interestCampus'] == "" || $data['interestCampus'] == '0' || strlen($data['interestCampus']) < 1){
            return array('success' => false, 'msg' => 'Elige un Campus', 'input' => 'interestCampus');
        }

        if(!isset($data['interestNivel']) || $data['interestNivel'] == "" || $data['interestNivel'] == '0' || strlen($data['interestNivel']) < 1){
            return array('success' => false, 'msg' => 'Elige una Área de Interés', 'input' => 'interestNivel');
        }

        if(!isset($data['citaCampus']) || $data['citaCampus'] == "" || $data['citaCampus'] == '0' || strlen($data['citaCampus']) < 1){
            return array('success' => false, 'msg' => 'Elige un Campus', 'input' => 'citaCampus');
        }

        if(!isset($data['tipificacion']) || $data['tipificacion'] == "" || $data['tipificacion'] == '0' || strlen($data['tipificacion']) < 1){
            return array('success' => false, 'msg' => 'Elige una Tipificación', 'input' => 'tipificacion');
        }

        //age,canal,cel,celRegis,citaAsesor,citaCall,citaCampus,citaTransfer,citadate,csq,gender,interes,interestArea,interestCampus,interestCareer,interestCycle,interestModel,interestNivel,mail,mailRegis,matern,maternRegis,name,nameRegis,note,patern,paternRegis,phone,phoneRegis,prent,sinmail,tipificacion,user


        return array('success' => true, 'msg' => '');
    }


    /**
     *
     * @REST\Post("/landing/register-promotion")
     * @return View
     */
    public function landingRegisterPromotionAction(Request $request) {

        $data = json_decode($request->getContent(), true);

        if(!isset($data['subActivity']) || $data['subActivity'] == "" || $data['subActivity'] == '0' || strlen($data['subActivity']) < 1){
            return array('success' => false, 'msg' => 'Elige un Sub tipo de Actividad', 'input' => 'subActivity');
        }

        if(!isset($data['company']) || $data['company'] == "" || $data['company'] == '0' || strlen($data['company']) < 1){
            return array('success' => false, 'msg' => 'Elige una Escuela/Empresa', 'input' => 'company');
        }

         if(!isset($data['subsubActivity']) || $data['subsubActivity'] == "" || $data['subsubActivity'] == '0' || strlen($data['subsubActivity']) < 1){
            return array('success' => false, 'msg' => 'Elige un Sub Sub tipo de Actividad', 'input' => 'subsubActivity');
        }

        if(!isset($data['tourn']) || $data['tourn'] == "" || $data['tourn'] == '0' || strlen($data['tourn']) < 1){
            return array('success' => false, 'msg' => 'Elige un Turno', 'input' => 'tourn');
        }

        if(!isset($data['school']) || $data['school'] == "" || $data['school'] == '0' || strlen($data['school']) < 1){
            return array('success' => false, 'msg' => 'Elige una Escuela/Empresa', 'input' => 'school');
        }

        if(!isset($data['name']) || $data['name'] == ""){
            return array('success' => false, 'msg' => 'Por favor ingresa un nombre', 'input' => 'name');
        }else{
            if(!$this->testName($data['name'])){
                return array('success' => false, 'msg' => 'Nombre inválido', 'input' => 'name');
            }
        }

        if(!isset($data['patern']) || $data['patern'] == "" || strlen($data['patern']) < 1){
            return array('success' => false, 'msg' => 'Por favor ingresa un apellido paterno', 'input' => 'patern');
        }else{
            if(!$this->testName($data['patern'])){
                return array('success' => false, 'msg' => 'Apellido Paterno inválido', 'input' => 'patern');
            }
        }

        if(!isset($data['matern']) || $data['matern'] == "" || strlen($data['matern']) < 1){
            return array('success' => false, 'msg' => 'Por favor ingresa un apellido materno', 'input' => 'matern');
        }else{
            if(!$this->testName($data['matern'])){
                return array('success' => false, 'msg' => 'Apellido Materno inválido', 'input' => 'matern');
            }
        }

        if(!isset($data['mail']) || $data['mail'] == "" || strlen($data['mail']) < 1){
            return array('success' => false, 'msg' => 'Por favor ingresa un correo válido', 'input' => 'mail');
        }else{
            $isFace = substr_count($data['mail'], 'facebook');
            $exp = explode("@",$data['mail']);
            $domain = explode(".",$exp[1]);
            $basura = $this->emailBasura($exp[0]);
            $basuraa = $this->emailBasura($domain[0]);

            if (!filter_var($data['mail'], FILTER_VALIDATE_EMAIL) || $isFace > '0' || $basura || $basuraa) {
                return array('success' => false, 'msg' => 'Correo Inválido', 'input' => 'mail');
            }
        }

        if(!isset($data['cel']) || $data['cel'] == "" || strlen($data['cel']) < 1){
            return array('success' => false, 'msg' => 'Ingresa número de Celular', 'input' => 'cel');
        }else{
            if(strlen($data['cel']) < 10 || !is_numeric($data['cel'])){
                return array('success' => false, 'msg' => 'Al menos debe contener 10 dígitos', 'input' => 'cel');
            }

            $rest = substr($data['cel'], 0, 6);
            $restt = substr($data['cel'], 4, 10);

            $isconsec = $this->numerosConsecutivos($rest);
            $isconsecc = $this->numerosConsecutivos($restt);


            if($isconsec || $isconsecc){
                return array('success' => false, 'msg' => 'Al menos debe contener 10 dígitos.', 'input' => 'cel');
            }
        }

        if(!isset($data['phone']) || $data['phone'] == "" || strlen($data['phone']) < 1){
            return array('success' => false, 'msg' => 'Ingresa número de Teléfono', 'input' => 'phone');
        }else{
            if(strlen($data['phone']) < 10 || !is_numeric($data['phone'])){
                return array('success' => false, 'msg' => 'Al menos debe contener 10 dígitos', 'input' => 'phone');
            }

            $rest = substr($data['phone'], 0, 6);
            $restt = substr($data['phone'], 4, 10);

            $isconsec = $this->numerosConsecutivos($rest);
            $isconsecc = $this->numerosConsecutivos($restt);


            if($isconsec || $isconsecc){
                return array('success' => false, 'msg' => 'Al menos debe contener 10 dígitos.', 'input' => 'phone');
            }
        }

        if(!isset($data['gender']) || $data['gender'] == "" || $data['gender'] == '0' || strlen($data['gender']) < 1){
            return array('success' => false, 'msg' => 'Elige un Género', 'input' => 'gender');
        }

        if(!isset($data['birthday']) || $data['birthday'] == "" || strlen($data['birthday']) < 1){
            return array('success' => false, 'msg' => 'Ingresa una Fecha de Nacimiento', 'input' => 'birthday');
        }

        if(!isset($data['age']) || $data['age'] == "" || strlen($data['age']) < 1){
            return array('success' => false, 'msg' => 'Ingresa una Edad', 'input' => 'age');
        }

        if(!isset($data['interestCampus']) || $data['interestCampus'] == "" || $data['interestCampus'] == '0' || strlen($data['interestCampus']) < 1){
            return array('success' => false, 'msg' => 'Elige un Campus', 'input' => 'interestCampus');
        }

        if(!isset($data['interestNivel']) || $data['interestNivel'] == "" || $data['interestNivel'] == '0' || strlen($data['interestNivel']) < 1){
            return array('success' => false, 'msg' => 'Elige una Área de Interés', 'input' => 'interestNivel');
        }


        return array('success' => true, 'msg' => '');
    }


    /**
     *
     * @REST\Post("/landing/register-solo")
     * @return View
     */
    public function landingRegisterSoloAction(Request $request) {

        $data = json_decode($request->getContent(), true);

        if(!isset($data['name']) || $data['name'] == ""){
            return array('success' => false, 'msg' => 'Por favor ingresa un nombre', 'input' => 'name');
        }else{
            if(!$this->testName($data['name'])){
                return array('success' => false, 'msg' => 'Nombre inválido', 'input' => 'name');
            }
        }

        if(!isset($data['patern']) || $data['patern'] == "" || strlen($data['patern']) < 1){
            return array('success' => false, 'msg' => 'Por favor ingresa un apellido paterno', 'input' => 'patern');
        }else{
            if(!$this->testName($data['patern'])){
                return array('success' => false, 'msg' => 'Apellido Paterno inválido', 'input' => 'patern');
            }
        }

        if(!isset($data['matern']) || $data['matern'] == "" || strlen($data['matern']) < 1){
            return array('success' => false, 'msg' => 'Por favor ingresa un apellido materno', 'input' => 'matern');
        }else{
            if(!$this->testName($data['matern'])){
                return array('success' => false, 'msg' => 'Apellido Materno inválido', 'input' => 'matern');
            }
        }

        if(!isset($data['mail']) || $data['mail'] == "" || strlen($data['mail']) < 1){
            return array('success' => false, 'msg' => 'Por favor ingresa un correo válido', 'input' => 'mail');
        }else{
            $isFace = substr_count($data['mail'], 'facebook');
            $exp = explode("@",$data['mail']);
            $domain = explode(".",$exp[1]);
            $basura = $this->emailBasura($exp[0]);
            $basuraa = $this->emailBasura($domain[0]);

            if (!filter_var($data['mail'], FILTER_VALIDATE_EMAIL) || $isFace > '0' || $basura || $basuraa) {
                return array('success' => false, 'msg' => 'Correo Inválido', 'input' => 'mail');
            }
        }

        if(!isset($data['cel']) || $data['cel'] == "" || strlen($data['cel']) < 1){
            return array('success' => false, 'msg' => 'Ingresa número de Celular', 'input' => 'cel');
        }else{
            if(strlen($data['cel']) < 10 || !is_numeric($data['cel'])){
                return array('success' => false, 'msg' => 'Al menos debe contener 10 dígitos', 'input' => 'cel');
            }

            $rest = substr($data['cel'], 0, 6);
            $restt = substr($data['cel'], 4, 10);

            $isconsec = $this->numerosConsecutivos($rest);
            $isconsecc = $this->numerosConsecutivos($restt);


            if($isconsec || $isconsecc){
                return array('success' => false, 'msg' => 'Al menos debe contener 10 dígitos.', 'input' => 'cel');
            }
        }

        if(!isset($data['phone']) || $data['phone'] == "" || strlen($data['phone']) < 1){
            return array('success' => false, 'msg' => 'Ingresa número de Teléfono', 'input' => 'phone');
        }else{
            if(strlen($data['phone']) < 10 || !is_numeric($data['phone'])){
                return array('success' => false, 'msg' => 'Al menos debe contener 10 dígitos', 'input' => 'phone');
            }

            $rest = substr($data['phone'], 0, 6);
            $restt = substr($data['phone'], 4, 10);

            $isconsec = $this->numerosConsecutivos($rest);
            $isconsecc = $this->numerosConsecutivos($restt);


            if($isconsec || $isconsecc){
                return array('success' => false, 'msg' => 'Al menos debe contener 10 dígitos.', 'input' => 'phone');
            }
        }

        if(!isset($data['gender']) || $data['gender'] == "" || $data['gender'] == '0' || strlen($data['gender']) < 1){
            return array('success' => false, 'msg' => 'Elige un Género', 'input' => 'gender');
        }

        if(!isset($data['birthday']) || $data['birthday'] == "" || strlen($data['birthday']) < 1){
            return array('success' => false, 'msg' => 'Ingresa una Fecha de Nacimiento', 'input' => 'birthday');
        }

        if(!isset($data['age']) || $data['age'] == "" || strlen($data['age']) < 1){
            return array('success' => false, 'msg' => 'Ingresa una Edad', 'input' => 'age');
        }

        if(!isset($data['interestCampus']) || $data['interestCampus'] == "" || $data['interestCampus'] == '0' || strlen($data['interestCampus']) < 1){
            return array('success' => false, 'msg' => 'Elige un Campus', 'input' => 'interestCampus');
        }

        if(!isset($data['interestNivel']) || $data['interestNivel'] == "" || $data['interestNivel'] == '0' || strlen($data['interestNivel']) < 1){
            return array('success' => false, 'msg' => 'Elige una Área de Interés', 'input' => 'interestNivel');
        }


        return array('success' => true, 'msg' => '');
    }


    /**
     *
     * @REST\Post("/landing/search")
     * @return View
     */
    public function landingSearchAction(Request $request) {

        $data = json_decode($request->getContent(), true);

        if(!isset($data['mail']) || $data['mail'] == "" || strlen($data['mail']) < 1){
            return array('success' => false, 'msg' => 'Por favor ingresa un correo válido', 'input' => 'mail');
        }else{
            $isFace = substr_count($data['mail'], 'facebook');
            $exp = explode("@",$data['mail']);
            $domain = explode(".",$exp[1]);
            $basura = $this->emailBasura($exp[0]);
            $basuraa = $this->emailBasura($domain[0]);

            if (!filter_var($data['mail'], FILTER_VALIDATE_EMAIL) || $isFace > '0' || $basura || $basuraa) {
                return array('success' => false, 'msg' => 'Correo Inválido', 'input' => 'mail');
            }
        }


        return array('success' => true, 'msg' => '');
    }


    /**
     *
     * @REST\Post("/landing/search-inbound")
     * @return View
     */
    public function landingSearchAInboundction(Request $request) {

        $data = json_decode($request->getContent(), true);

        if(!isset($data['mail']) || $data['mail'] == "" || strlen($data['mail']) < 1){
            return array('success' => false, 'msg' => 'Por favor ingresa un correo válido', 'input' => 'mail');
        }else{
            $isFace = substr_count($data['mail'], 'facebook');
            $exp = explode("@",$data['mail']);
            $domain = explode(".",$exp[1]);
            $basura = $this->emailBasura($exp[0]);
            $basuraa = $this->emailBasura($domain[0]);

            if (!filter_var($data['mail'], FILTER_VALIDATE_EMAIL) || $isFace > '0' || $basura || $basuraa) {
                return array('success' => false, 'msg' => 'Correo Inválido', 'input' => 'mail');
            }
        }


        return array('success' => true, 'msg' => '');
    }


    function testName($name){
        $name = str_replace("ñ", "", $name);
        $name = str_replace("Ñ", "", $name);
        $return = true;
        $posa = strpos($name, 'a');
        $pose = strpos($name, 'e');
        $posi = strpos($name, 'i');
        $poso = strpos($name, 'o');
        $posu = strpos($name, 'u');
        $posaa = strpos($name, 'A');
        $posee = strpos($name, 'E');
        $posii = strpos($name, 'I');
        $posoo = strpos($name, 'O');
        $posuu = strpos($name, 'U');

        $spaces = substr_count($name, ' ');
        if (!preg_match("/^[a-zA-Z ]*$/",$name) || strlen($name) < 3 || $spaces > 1 || (!$posa && !$pose && !$posi && !$poso && !$posu && !$posaa && !$posee && !$posii && !$posoo && !$posuu)) {
            $return = false;
        }

        return $return;
    }

    function numerosConsecutivos($num){
        $numeros = str_split($num);
        $numItems = count($numeros);
        $i = 0;
        foreach ($numeros as $clave => $valor) {
            if(++$i != $numItems) {
                if($numeros[$clave+1]-$numeros[$clave] != 1) return false;
            }
        }

        return true;
    }


    function palabrasBasura($text){
        $palabrasJson = [
            "alkj",
            "balk",
            "blaj",
            "blal",
            "ccfe",
            "cerc",
            "cfew",
            "crcw",
            "cten",
            "cwef",
            "cwer",
            "dgji",
            "dhdn",
            "djuo",
            "dmjc",
            "efcc",
            "ehew",
            "ercw",
            "erhu",
            "eruh",
            "fccf",
            "fdgj",
            "fdju",
            "ffgf",
            "fgff",
            "fgfg",
            "fghu",
            "fyut",
            "gfdg",
            "gffg",
            "gfgf",
            "ghui",
            "gjif",
            "gyhu",
            "gyuw",
            "hdnv",
            "hewr",
            "huik",
            "huiw",
            "huwe",
            "ifdj",
            "igsd",
            "ikty",
            "iwre",
            "jbla",
            "jcte",
            "jifd",
            "jigs",
            "jksd",
            "juoi",
            "kjbl",
            "kqnw",
            "kqwk",
            "ksdm",
            "ktyf",
            "kuyt",
            "lajk",
            "lbal",
            "lkjb",
            "lsdm",
            "m sk",
            "mdhj",
            "nvji",
            "oidf",
            "rcer",
            "rcwe",
            "rhue",
            "rhui",
            "ruhu",
            "sdhd",
            "sdmd",
            "sdmj",
            "skqn",
            "skqw",
            "swqn",
            "tenv",
            "tfgh",
            "tyfy",
            "uerh",
            "uerw",
            "ugfd",
            "uhuw",
            "uikt",
            "uiwr",
            "uoid",
            "uwer",
            "uytf",
            "vjig",
            "wefc",
            "werc",
            "werh",
            "weru",
            "wqnm",
            "wreh",
            "yfyu",
            "yhue",
            "ytfg",
            "yugf",
            "yutf",
            "yuwe",
            "fgsd",
            "ergs",
            "grty",
            "drgd",
            "alsd",
            "mjct",
            "envj",
            "djks",
            "dmdh",
            "jsdh",
            "dnv",
            "xzzf",
            "afsa",
            "tdg",
            "gsz",
            "sz",
            "duhe",
            "edc",
            "cgh",
            "ghd",
            "turru",
            "rurt",
            "fgj",
            "gjg",
            "gfk",
            "glh",
            "huj",
            "shd",
            "bcc",
            "hshs",
            "djdj",
            "asda",
            "fdfd",
            "dxxc",
            "hhhh",
            "bxcb",
            "blabla",
            "bloblo",
            "aloh",
            "nrnr",
            "fgdg",
            "ffyf",
            "baaf",
            "ddcc",
            "ffdd",
            "sjsn",
            "sfdd",
            "fqef",
            "fsff",
            "ddxc",
            "gdgf",
            "hfcb",
            "gjdj",
            "hxgh",
            "xhgx",
            "bhbh",
            "kjhh",
            "bsbs",
            "klhl",
            "rqrq",
            "kekd",
            "sasa",
            "sisi",
            "reret",
            "xdsg",
            "abec",
            "fefe",
            "webo",
            "nyolx",
            "ccgjh",
            "fadf",
            "bulabe",
            "efq",
            "luego",
            "erdem",
            "juanbenito",
            "yolo",
            "ddsv",
            "memo",
            "fulanito",
            "hfcbbi",
            "hjdjh",
            "rirdfo",
            "hisjd",
            "ezra",
            "vcb",
            "taewe",
            "http",
            "fsdgfsd",
            "pancho",
            "lokon",
            "izhmir",
            "gmbo",
            "tito",
            "erwq",
            "jhk",
            "aaee",
            "oeds",
            "milki",
            "oskr",
            "sunn",
            "rerer",
            "nora vivia",
            "retrdtr",
            "krmn",
            "hrth",
            "afwef",
            "codision",
            "ytufut",
            "rwerwere",
            "akiva",
            "jeziel",
            "bla bla",
            "raraaf",
            "kbd",
            "dage",
            "dgfg",
            "frfregetgr",
            "yuya",
            "fuif",
            "xghij",
            "wegwgr",
            "vhg",
            "gvg",
            "fdghf",
            "luigui",
            "luialmanza",
            "asdasdas",
            "xereerer",
            "puta madre",
            "gdfgd",
            "matafesio",
            "ttgt",
            "bfbf",
            "ahhesaqs",
            "asdda",
            "yolia",
            "sedd",
            "dffgt",
            "tutsi",
            "fefreferfr",
            "assaasa",
            "sfdzser",
            "chucho",
            "ghfg",
            "alfito",
            "ckbad",
            "huhbgvf",
            "adaw",
            "wwdd",
            "asumen",
            "xdsgdg",
            "martona",
            "chanfle",
            "dfgdf",
            "hhjhge",
            "dnjs",
            "hhj",
            "kajs",
            "jsjs",
            "jajaj",
            "aasaa",
            "aafs",
            "only",
            "networ",
            "agah",
            "shsh",
            "jjs",
            "gghh",
            "bbhh",
            "nkoh",
            "klhj",
            "gdgd",
            "xret",
            "xrre",
            "kml",
            "iolk",
            "tengo",
            "pgom",
            "cst",
            "suhh",
            "ghf",
            "pepito",
            "iuni",
            "ujiu",
            "baby",
            "lolo",
            "hardy",
            "control",
            "desconocido",
            "apocope",
            "zvzc",
            "csaf",
            "wars",
            "atsv",
            "qeax",
            "gasf",
            "csaz",
            "klkl",
            "rowe",
            "ejrl",
            "dsc",
            "nivx",
            "djfa",
            "eoir",
            "kdlf",
            "fsdl",
            "kwje",
            "xlcv",
            "rtuy",
            "jcvn",
            "qazx",
            "swed",
            "dcvf",
            "vrtg",
            "byhn",
            "mjui",
            "iklo",
            "fdfg",
            "yuiop",
            "fghj",
            "jklm",
            "zxcv",
            "zxcvb",
            "xcvb",
            "cvbn",
            "vbnm",
            "mnbv",
            "cxza",
            "fdsa",
            "kjhg",
            "lkjh",
            "poiu",
            "iuuyt",
            "rewq",
            "wqwqw",
            "qwqw",
            "zqwd",
            "qwdd",
            "wksn",
            "ksnj",
            "snjs",
            "nsns",
            "snsn",
            "woie",
            "oier",
            "ssgg",
            "hjje",
            "jjee",
            "wwee",
            "eerr",
            "ttyy",
            "yyuu",
            "tyyu",
            "aass",
            "assd",
            "ssdd",
            "soso",
            "dsew",
            "sewq",
            "ewqe",
            "wqew",
            "qewt",
            "eqwt",
            "qwte",
            "wtew",
            "ewew",
            "wewq",
            "ewqw",
            "wqwq",
            "aegs",
            "egsb",
            "gsbg",
            "jkty",
            "ktyt",
            "tyty",
            "ytyj",
            "tyjr",
            "ewer",
            "fdd",
            "kbak",
            "ijus",
            "ppe",
            "edau",
            "dgh",
            "wdc",
            "ksdy",
            "fetg",
            "didd",
            "sdvs",
            "asaf",
            "ghh",
            "xxx",
            "msus",
            "fsd",
            "tuyt",
            "elver",
            "jsks",
            "drozz",
            "lmm",
            "leet",
            "hfc",
            "ccxx",
            "fff",
            "hlek",
            "hjja",
            "pee",
            "ryyy",
            "viro",
            "ajaj",
            "utbb",
            "psm",
            "djah",
            "msm",
            "brgb",
            "kik",
            "vbv",
            "ghg",
            "tdfy",
            "xhkf",
            "aqwew",
            "kll",
            "sdd",
            "test",
            "jgkk",
            "aaad",
            "abb",
            "mylady",
            "fgg",
            "adfw",
            "mmnj",
            "jhgs",
            "ffkc",
            "idj",
            "ernej",
            "abc",
            "adk",
            "cdcd",
            "xasf",
            "jkk",
            "adds",
            "frr",
            "gyuu",
            "ffca",
            "ftgh",
            "ffsf",
            "aasds",
            "afdsjkl",
            "asdrubal",
            "xxasd",
            "asfcasdc",
            "fewrwererwee",
            "hfs",
            "afa",
            "ayoung",
            "fddsadas",
            "xcasd",
            "ewrwer",
            "fjddjv",
            "adfwf",
            "nohh",
            "tzuc",
            "lumbi",
            "xadsa",
            "werwer",
            "dgvxgv",
            "afoaisnfona",
            "noh",
            "zib",
            "vgfyhjrfyje",
            "asdf",
            "maruio",
            "mander",
            "mades",
            "gayluis",
            "gaylor",
            "asdk",
            "ajfi",
            "ojss",
            "cxbcvn",
            "oacr",
            "line",
            "atom",
            "aksis",
            "jolik",
            "veic",
            "knight",
            "caca",
            "orina",
            "mierda",
            "asqueroso",
            "apellido",
            "qwrqwweq",
            "superman",
            "culiada",
            "manches",
            "maricon",
            "pendejo",
            "batman",
            "marica",
            "ramera",
            "skulls",
            "dadsa",
            "dffdd",
            "holis",
            "mamar",
            "nalga",
            "perra",
            "tinaa",
            "kdfh",
            "jslk",
            "dlkf",
            "wieo",
            "completonopasa",
            "cnvm",
            "trial",
            "iuiou",
            "aeiou",
            "adsf",
            "culo",
            "jaja",
            "joto",
            "nomb",
            "pito",
            "puta",
            "puto",
            "xoxo",
            "verga",
            "nene",
            "dada",
            "baboso",
            "pantera",
            "pene",
            "popo",
            "tu puta madre",
            "chinga",
            "zqwdd",
            "web",
            "gwer",
            "dfgh",
            "dfgha",
            "fgh",
            "wweerr",
            "sososo",
            "aegsbg",
            "wksnjs",
            "woier",
            "segibia",
            "dassgg",
            "ttyyuu",
            "qwer",
            "dsewqewt",
            "goerd",
            "nsnsn",
            "ajuer",
            "hjjee",
            "aassdd",
            "eqwtewewqwq",
            "jktytyjr",
            "chula",
            "perdo",
            "lopo",
            "heintricu",
            "rocken",
            "shazam",
            "elver",
            "coca",
            "nombre",
            "rick",
            "kiko",
            "moco",
            "moshe",
            "privado",
            "kimo",
            "contacto",
            "sabe",
            "perro",
            "demos",
            "tutu",
            "sionsa",
            "ninguno",
            "poseidon",
            "asf",
            "cola",
            "pancho",
            "abombado",
            "asds",
            "dhhgdd",
            "oiidl",
            "xhhjh",
            "ooiddl",
            "fhhjse"
        ];

        $return = array_search($text, $palabrasJson);

        return $return;
    }

    function emailBasura($text){
        $basura = [
            "anonimo",
            "sdasa",
            "sadasdas4",
            "sdfsd",
            "gay",
            "rari",
            "ajshs",
            "aad",
            "ads",
            "aea",
            "ahh",
            "apellido",
            "asf",
            "asj",
            "bbb",
            "bfj",
            "bjk",
            "bvh",
            "ccc",
            "cds",
            "chc",
            "cju",
            "csd",
            "cvb",
            "cxz",
            "dadsa",
            "ddd",
            "dee",
            "demo",
            "dfg",
            "djf",
            "djj",
            "dnd",
            "drh",
            "dse",
            "dsf",
            "dss",
            "dsx",
            "dvc",
            "dxx",
            "dyy",
            "eee",
            "eeh",
            "ewf",
            "fff",
            "fgg",
            "fgt",
            "fhg",
            "fjg",
            "fjh",
            "fkj",
            "fnh",
            "frg",
            "gfl",
            "ggg",
            "ghb",
            "ghh",
            "ghk",
            "gjs",
            "gvk",
            "hah",
            "hch",
            "hdj",
            "hgd",
            "hgf",
            "hhf",
            "hhh",
            "hjk",
            "hjn",
            "hola",
            "ibf",
            "ihi",
            "iii",
            "iji",
            "ioj",
            "iph",
            "jaj",
            "jaja",
            "jhg",
            "jhj",
            "jhn",
            "jhr",
            "jhu",
            "jjj",
            "jjn",
            "jkl",
            "jlk",
            "jok",
            "kbe",
            "khv",
            "kjk",
            "kjn",
            "kjs",
            "kkj",
            "kkk",
            "klk",
            "knk",
            "lkh",
            "lkj",
            "llj",
            "lll",
            "lol",
            "luu",
            "mkk",
            "mmm",
            "mpm",
            "nalga",
            "nel",
            "njp",
            "nkj",
            "nnn",
            "nomb",
            "nul",
            "oif",
            "oio",
            "ooo",
            "ouo",
            "paa",
            "personal",
            "pito",
            "ppp",
            "qqq",
            "qwe",
            "rgt",
            "rrr",
            "rtr",
            "sda",
            "sdf",
            "sds",
            "sfj",
            "sss",
            "sws",
            "trial",
            "trr",
            "trt",
            "ttt",
            "uuu",
            "vee",
            "vvv",
            "wfe",
            "www",
            "xcv",
            "xde",
            "xxx",
            "xzz",
            "yrt",
            "yyy",
            "zxc",
            "zye",
            "zzz",
            "none",
            "asas",
            "fds",
            "jnk",
            "jll",
            "rrb",
            "perra",
            "puta",
            "culo",
            "culiada",
            "marica",
            "mamar",
            "puto",
            "pendejo",
            "ldk",
            "xoxo",
            "ramera",
            "hff",
            "verga",
            "maricon",
            "campell",
            "eqtynhxfdm",
            "qwrqwweq",
            "dfhjgjm",
            "eewer",
            "jklyg",
            "asrfggd",
            "hhsdfdgddh",
            "qwert",
            "kjhgf",
            "abcde12345",
            "12356hgtght",
            "qwde",
            "facebook",
            "haqed",
            "hormail",
            "homail",
            "gamil",
            "cdnqa",
            "hitmail",
            "einrot",
            "gmial",
            "gmil",
            "hotmial",
            "superrito",
            "drisd",
            "htomail",
            "hotmil",
            "hotmal",
            "gamail",
            "goeqa",
            "dayrep",
            "jotmail",
            "outlok",
            "armyspy",
            "fleckens",
            "gustr",
            "jourrapide",
            "rhyta",
            "teleworm",
            "kmhow",
            "zoho",
            "hptmail",
            "hoymail",
            "htmail",
            "hotnail",
            "yopmail",
            "my10minutemail",
            "yomail",
            "20email",
            "trbvn",
            "10minutemail",
            "swift10minutemail",
            "mailinator",
            "meltmail",
            "TempEMail",
            "filzmail",
            "sharklasers",
            "guerrillamail",
            "grr",
            "guerrillamailblock",
            "spam4",
            "novalido"
        ];

        $return = array_search($text, $basura);

        return $return;
    }


}
