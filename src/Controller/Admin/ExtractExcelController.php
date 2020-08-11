<?php
namespace App\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use App\Repository\UserRepository;

use Symfony\Component\Intl\Intl;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


/**
 * @Route("excel")
 * @Security("is_granted('ROLE_USER_MANAGER') or is_granted('ROLE_SUPER_ADMIN')")
 */
class ExtractExcelController extends Controller
{
	/**
	 * @Route("/emails", name="admin_extract_emails", methods="GET|POST")
	 *
	 * @param UserRepository $userRepository
	 * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
	 * @throws \PhpOffice\PhpSpreadsheet\Exception
	 * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
	 */
   public function extractEmails(UserRepository $userRepository)
   {
     $users = $userRepository->findAll();
     $targetFile = join(DIRECTORY_SEPARATOR, [$this->getParameter('extract_path'), 'users_info.xlsx']);

     $spreadsheet = new Spreadsheet();
     $sheet = $spreadsheet->getActiveSheet();
     $sheet->setCellValue('A1', 'NOM');
     $sheet->setCellValue('B1', 'PRENOM');
     $sheet->setCellValue('C1', 'COURRIEL');
     $sheet->setCellValue('D1', 'COURRIEL SECONDAIRE');
     $sheet->setCellValue('E1', 'ADRESSE');
     $sheet->setCellValue('F1', 'PAYS');

     $styleArray = ['font' => ['bold' => true]];

     $sheet->getStyle('A1:F1')->applyFromArray($styleArray);

     foreach (range('A','G') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
     }

     foreach($users as $key=>$user)
     {
       $key += 2;
       $sheet->setCellValue('A'.$key, $user->getLastName());
       $sheet->setCellValue('B'.$key, $user->getFirstName());
       $sheet->setCellValue('C'.$key, $user->getEmail());
       $sheet->setCellValue('D'.$key, $user->getEmailSecondary());
       $sheet->setCellValue('E'.$key, $user->getBillingAddress()->getAddress());
       $sheet->setCellValue('F'.$key, Intl::getRegionBundle()->getCountryName($user->getBillingAddress()->getCountry(), 'fr'));
     }

     $writer = new Xlsx($spreadsheet);
     $writer->save($targetFile);

     return $this->file($targetFile);
   }
}
