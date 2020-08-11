<?php

/*
 * This file is part of the Emmobilier project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Utils;

use Symfony\Component\Form\FormInterface;

/**
 * Tools Provides basic collection of useful PHP functions.
 */
class Tools
{
    /**
     * Set price filter
     *
     * @param float|null $number
     * @param int $decimals
     * @param string $decPoint
     * @param string $thousandsSep
     * @return string
     */
    public static function priceFormater(?float $number, int $decimals = 2, string $decPoint = '.', string $thousandsSep = ','): string
    {
        return number_format($number, $decimals, $decPoint, $thousandsSep);
    }

    /**
     * Convert a multi-dimensional array into a single-dimensional array.
     *
     * @param  array $array The multi-dimensional array.
     * @return array
     */
    public static function arrayFlatten(array $array): array
    {
        if (!is_array($array)) {
            return false;
        }
        $result = array();
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $result = array_merge($result, self::arrayFlatten($value));
            } else {
                $result[$key] = $value;
            }
        }
        return $result;
    }

    /**
     * Converts a given size with units to bytes.
     *
     * @param string $str
     *
     * @return string
     */
    public static function toBytes(string $str): ?string
    {
        $str = trim($str);
        $last = strtolower($str[strlen($str)-1]);
        $val = 0;
        if (is_numeric($last)) {
            $val = (int) $str;
        } else {
            $val = (int) substr($str, 0, -1);
        }
        switch ($last) {
            case 'g': case 'G': $val *= 1024;
            // no break
            case 'm': case 'M': $val *= 1024;
            // no break
            case 'k': case 'K': $val *= 1024;
        }
        return $val;
    }

    /**
     * Destroys the specified variables form array.
     *
     * @param array   $objects
     * @param string  $id
     *
     * @return array
     */
    public static function findOneByID(array $objects, string $id)
    {
        foreach ($objects as $object) {
            if ($object->getId() == $id) {
                return $object;
            }
        }
        return null;
    }

    /**
     * Return the dates between two date time.
     *
     * @param \DateTimeInterface $startDate
     * @param \DateTimeInterface $endDate
     * @param string $format
     *
     * @return array
     * @throws \Exception
     */
    public static function dateInterval(\DateTimeInterface $startDate, \DateTimeInterface $endDate, string $format = "d-m-Y"): array
    {
    	/** @var \DateTimeInterface[] $dateRange */
        $dateRange = new \DatePeriod($startDate, new \DateInterval('P1D'), $endDate);
        $days = [];
        foreach ($dateRange as $date) {
            $days[] = str_replace('"', "'", $date->format($format));
        }
        return $days;
    }

    /**
     * Return decoded string.
     *
     * @param string $str
     *
     * @return string
     */
    public static function UTFDecode(?string $str=''): string
    {
        return (string) iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', stripslashes(utf8_decode($str)));
    }

    /**
     * @param FormInterface $form
     * @return array
     */
    public static function getErrorMessages(FormInterface $form)
    {
        $errors = array();
        foreach ($form->getErrors() as $error) {
            $errors[] = $error->getMessage();
        }

        foreach ($form->all() as $childForm) {
            if ($childForm instanceof FormInterface) {
                if ($childErrors = self::getErrorMessages($childForm)) {
                    $errors[] = $childErrors;
                }
            }
        }
        return $errors;
    }
	
	/**
	 * @param FormInterface $form
	 * @return array
	 */
	public static function getErrorMessagesWithLabels(FormInterface $form)
	{
		$errors = array();
		foreach ($form->getErrors() as $error) {
			$errors[] = $error->getMessage();
		}
		
		foreach ($form->all() as $childForm) {
			if ($childForm instanceof FormInterface) {
				if ($childErrors = self::getErrorMessages($childForm)) {
					$errors[$childForm->getConfig()->getOption('label')] = $childErrors;
				}
			}
		}
		return $errors;
	}

    /**
     * Get Error Messages From the Form with keys.
     *
     * @param FormInterface $form The current form that contains errors
     *
     * @return array The array of errors
     */
    public static function getFromErrorMessages(FormInterface $form): array
    {
        static $constraints = [];

        $errorMsg = self::getErrorMessage($form);
        if (!is_null($errorMsg) && $form->isRoot()){
            $constraints[is_numeric($form->getName()) ? "form" : $form->getName()][] = [
                'constraints' => 'The current order object is malformatted'
            ];
        }elseif (!is_null($errorMsg)){
            $constraints[is_numeric($form->getName()) ? "form" : $form->getName()][] = $errorMsg;
        }

        foreach ($form->all() as $child) {
            $errorMsg = self::getErrorMessage($child);
            if (count($child->all()) == 0 && !is_null($errorMsg)){
                $constraints[$child->getName()][] = $errorMsg;
            }else{
                self::getFromErrorMessages($child);
            }
        }

        return empty($constraints) ? [] : $constraints;
    }

    /**
     * @param FormInterface $form
     * @return array|null
     */
    private static function getErrorMessage (FormInterface $form): ?array
    {
        $constraints = [];
        foreach ($form->getErrors() as $error) {
            $constraints[] = $error->getMessage();
        }

        if (count($constraints) > 0){
            return ['data' => $form->getViewData(), 'constraints' => $constraints];
        }
        return null;
    }
}
