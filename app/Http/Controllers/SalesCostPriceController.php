<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\TableController;

class SalesCostPriceController extends Controller
{
    // ========================================================
    // ==================== Form Page Tab1 ====================
    // ========================================================

    public function salesCostPricePage()
    {
        $pageId = 476;
        $tabId = 1;
        $css = '';

        

        $this->setPage($pageId, $tabs=[], $tabId);

        /////////////// Table Head /////////////

        $tableHead = $this->tableHeadx($pageId,$tabId);

        $tableBody = collect($tableBody = []);

        $this->tablePage($tableHead, $tableBody);
        $arrayDrowInfo = [
            'row' => '',
            'pageId' => $pageId,
            'tabId' => $tabId,
        ];
        return $this->setView('sales_cost_price.MainPage',$arrayDrowInfo);
    }

    // ================== Search Tab1 ====================
    public function salesCostPriceSearch(Request $request)
    {
        $pageId = 476;
        $tabId = 1;
        $orgHead = session('orgHead');
        $orgId = session('orgId');
        $group_id = session('groupId');
        $curOrg = session('curOrg');

        $formData = $request->formData;
        $formData = json_encode($request->formData);
        $formData = json_decode($formData);
        /////////////// Start search /////////////
        $conditionsGeneral = "";
        $index_name_id_v = "";

        if (isset($formData)) {
            foreach ($formData as $key => $item) {

                if ($item->name == 'entry_time01' && !empty($item->value)) {
                    $cl_start_date = $item->value;
                    if (empty($cl_start_date))
                        $cl_start_date =  Carbon::now()->format('Y-m-d');
                    $conditionsGeneral .= " and  som.date  >= '" . $cl_start_date . "' ";
                } elseif ($item->name == 'entry_time03') {

                    $cl_end_date = $item->value;
                    if (empty($cl_end_date))
                        $cl_end_date =  Carbon::now()->format('Y-m-d');
                    if (empty($cl_start_date))
                        $cl_start_date =  Carbon::now()->format('Y-m-d');
                    $conditionsGeneral .= " and som.date <= '" . $cl_end_date . "' ";
                }

                // اسم الصنف
                if ($item->name == 'cl_item_name' && !empty($item->value)) {
                    $cl_item_name_n = $item->name;
                    $cl_item_name_v = $item->value;
                    $conditionsGeneral .= " and item.cl_index = '" . $item->value . "' ";
                }

                // رقم الحركة من
                if ($item->name == 'move_from' && !empty($item->value)) {
                    $move_from_n = $item->name;
                    $move_from_v = $item->value;
                    // dd($item->value);
                    $conditionsGeneral .= " and som2.serial_id  >= '" . $item->value . "' ";
                }

                if ($item->name == 'move_to' && !empty($item->value)) {
                    $move_to_n = $item->name;
                    $move_to_v = $item->value;
                    $conditionsGeneral .= " and som2.serial_id <= '" . $item->value . "' ";
                }

                // اسم المستلم
                if ($item->name == 'sale_man_a' && !empty($item->value)) {
                    $sale_man_n = $item->name;
                    $sale_man_v = $item->value;
                    $conditionsGeneral .= " and som.sale_man = '" . $item->value . "' ";
                }

                // نوع الحركة
                if ($item->name == 'transeaction_type' && !empty($item->value)) {
                    $transeaction_type_n = $item->name;
                    $transeaction_type_v = $item->value;
                    $conditionsGeneral .= " and som.transeaction_type = '" . $item->value . "' ";
                }

                // اسم العميل
                if ($item->name == 'index_name_id' && !empty($item->value)) {
                    $index_name_id_n = $item->name;
                    $index_name_id_v = $item->value;
                    $conditionsGeneral .= " and som.customer_name = '" . $item->value . "' ";
                }

                // عرض التائج
                if ($item->name == 'results_show' && !empty($item->value)) {
                    $index_name_id_n = $item->name;
                    $index_name_id_v = $item->value;
                    // dd($index_name_id_v);
                }
                if ($item->name == 'groupBy' && !empty($item->value)) {
                    $groupBy_n = $item->name;
                    $groupBy_v = $item->value;
                    // dd($index_name_id_v);
                }
            }
        }
        /////////////// End search /////////////

        /////////////// Table Head /////////////
        if (session('language') == 'rtl') {
            $colName = 'l.cl_ar_name As name';
        } else {
            $colName = 'l.cl_en_name as name';
        }

         $notInTable = [];
        if ($index_name_id_v == 645)
            $notInTable=['3209', '2495', '1509', '2707', '2348', '2009','6287'];

    

        if ($index_name_id_v == 646) // تفصيلي
             $notInTable= ['5186', '5187','7738','7739','7740','7744','7745'];
         

        // dd($tableHead);


        /////////////// Table Body /////////////
        $tableBody =
            DB::unprepared(
                DB::raw("

                CREATE temporary TABLE `sale_order_details_temp` (
                    `id` int(11),
                    `shipping_type` int(11) DEFAULT 0,
                    `master_id` bigint(20),
                    `cl_index` int(11) DEFAULT 0,
                    `item_supplier_id` varchar(100) DEFAULT NULL,

                    `details_id` int(11) DEFAULT 0,
                    `from_id` int(11) DEFAULT 0,
                    `requestQuant` decimal(36,18) DEFAULT 0.000000000000000000,
                    `quantity_out` decimal(36,18) DEFAULT 0.000000000000000000,
                    `quant_my_branch` decimal(36,18) DEFAULT 0.000000000000000000,
                    `quant_other_branch` decimal(36,18) DEFAULT 0.000000000000000000,

                    `boon` decimal(36,18) DEFAULT 0.000000000000000000,
                    `price` decimal(36,18) DEFAULT 0.000000000000000000,
                    `unit` varchar(100) DEFAULT NULL,
                    `unit_val` decimal(36,18) DEFAULT 1.000000000000000000,
                    `the_basic_unit` int(11),
                    `basic_unit_value` decimal(36,18) DEFAULT 0.000000000000000000,
                    `store_id` int(11) DEFAULT 0,
                    `price_list` int(11) DEFAULT 0,
                    `price_list__price` decimal(36,18) DEFAULT 0.000000000000000000,

                    `cust_id` int(11) DEFAULT 0,
                    `sale_man` varchar(100) DEFAULT 0,
                    `page_id` int(11) DEFAULT 0,
                    `quantity_out_last` decimal(36,18) DEFAULT 0.000000000000000000,
                    `discountUnit` decimal(36,18) DEFAULT 0.000000000000000000,
                    `total_comprehensive_discount` decimal(36,18) DEFAULT NULL,
                    `costing` decimal(36,18) DEFAULT 0.000000000000000000,
                    `inventory_quantity` decimal(36,18) DEFAULT 0.000000000000000000,
                    `barcode_id` int(11) DEFAULT 0,
                    `local_id` int(11) DEFAULT 0,

                    `quantity_in` decimal(36,18) DEFAULT 0.000000000000000000,
                    `tax_percent` decimal(25,15) DEFAULT 5.000000000000000,
                    `unit_name_ar` varchar(100) DEFAULT NULL,
                    `unit_name_en` varchar(100) DEFAULT NULL,
                    `first_sale_price_vat` decimal(30,10) DEFAULT 0.0000000000,
                    `average_price` decimal(30,10) DEFAULT 0.0000000000,
                    `cl_item_name` varchar(100) DEFAULT NULL,

                    `date` date  ,
                    `sale_price_avg` decimal(36,18) DEFAULT NULL,
                    `avg_cost` decimal(36,18) DEFAULT NULL,
                    `profit_avg` decimal(36,18) DEFAULT NULL,
                    `total_additional_cost` decimal(36,18) DEFAULT NULL,

                    `quantity` decimal(36,18) DEFAULT 0.000000000000000000,
                    `transeaction_type` varchar(100) DEFAULT NULL,
                    `net_profit` decimal(36,18) DEFAULT 0.000000000000000000,
                    `prime_cost` decimal(36,18) DEFAULT 0.000000000000000000,
                    `selling_price` decimal(36,18) DEFAULT 0.000000000000000000,
                    `serial_id1`  varchar(100) DEFAULT NULL

                  ) ENGINE=MyISAM DEFAULT CHARSET=utf8;

                ")
            );

        if ($index_name_id_v == 645) {  //الاجمالي

            if ($groupBy_v == 902)
                $groupBy = 'sod.cl_index';
            else if ($groupBy_v == 903)
                $groupBy = 'som2.customer_name';
            else if ($groupBy_v == 904)
                $groupBy = 'som2.id';
            else if ($groupBy_v == 905)
                $groupBy = 'som2.sale_man';
            else
                $groupBy = 'som2.customer_name';

            if ($tableBody) {

                $sql = "
                insert into sale_order_details_temp(
                cl_index,quantity_out,sale_price_avg,avg_cost,profit_avg,total_additional_cost )

                select ".$groupBy.",
                sum(sod.quantity_out),
                sum(sod.quantity_out*price-sod.quantity_out*sod.discountUnit+sod.quantity_out*sod.price*sod.tax_percent/100 ) sale_price_avg,
                sum(sod.average_price*sod.quantity_out*sod.unit_val/tu.cl_value) avg_cost,
                sum(sod.quantity_out*price-sod.quantity_out*sod.discountUnit+sod.quantity_out*sod.price*sod.tax_percent/100-sod.average_price*sod.quantity_out*sod.unit_val/tu.cl_value -sod.total_additional_cost) profit_avg,
                sum(sod.total_additional_cost) total_additional_cost

                from `sale_order_details_tb` sod
                    join sale_order_master_tb som on sod.master_id = som.id and som.page_id = 445 and som.page_id = 445 
                    join tb_item item on sod.Cl_index = item.Cl_index
                    join tb_item_org tio on tio.Cl_index = sod.Cl_index and tio.org_id = 1
                    join sale_order_master_tb som2 on som.details_id = som2.id and som2.page_id = 314 
                    join tb_units tu on tu.id = tio.the_basic_unit WHERE 1=1 " . $conditionsGeneral . " group by ".$groupBy;

                // $sql .= $conditionsGeneral;
              //  dump($sql);
                DB::unprepared(
                    $query = DB::raw($sql)
                );

              if ($groupBy_v == 902)
                {
                     $sql = "update sale_order_details_temp sodt
                    join tb_item item on sodt.Cl_index = item.Cl_index
                set
                    sodt.item_supplier_id = item.item_supplier_id,
                    sodt.cl_item_name = item.cl_item_name;
                ";
                $notInTable = array_merge($notInTable,['7738','7739','7740','7744','7745']);
                }
            else if ($groupBy_v == 903)
                {
                     $sql = "update sale_order_details_temp sodt
                    join customer_sale item on sodt.Cl_index = item.cust_id
                set
                    sodt.item_supplier_id = item.serial_id,
                    sodt.cl_item_name = item.customer_name_ar;";
                $notInTable = array_merge($notInTable,['7738','2382','643','7744','7745']);
                }
            else if ($groupBy_v == 904)
               {
                $sql = "update sale_order_details_temp sodt
                    join sale_order_master_tb item on sodt.Cl_index = item.id
                set
                    sodt.item_supplier_id = item.serial_id,
                    sodt.cust_id = item.customer_name;";

                

                $notInTable = array_merge($notInTable,['2382','643','7739','7744','7745']);
                }
                else if($groupBy_v == 905){

                    $sql = "update sale_order_details_temp sodt
                    join salesman_tb item on sodt.cl_index = item.id
                        set
                    sodt.cl_item_name = item.ar_name,
                    sodt.item_supplier_id = item.serial_id;";
                $notInTable = array_merge($notInTable,['2382','643','7739','7738','7740']);
                }
            else{

                $groupBy = 'som2.customer_name';
            }

               

                DB::unprepared(
                    $query = DB::raw($sql)
                );

                $sql1 = "update sale_order_details_temp sodt
                    join customer_sale item on sodt.cust_id = item.cust_id
                set
                    sodt.cl_item_name = item.customer_name_ar;";
                 DB::unprepared($query = DB::raw($sql1));
            }

         //   $tableBody = DB::table('sale_order_details_temp')->get();


            /////////////// View Table  /////////////

            $tableBody =  DB::table('sale_order_details_temp as sodt')
                ->select(
                    'sodt.cl_index as control',
                    'sodt.item_supplier_id as item_supplier_id',
                    'sodt.cl_item_name as cl_item_name',
                    DB::raw('ROUND(sodt.quantity_out,2) as quantity'),
                    DB::raw('ROUND(sodt.sale_price_avg,2) as sale_price_avg'),
                    DB::raw('ROUND(sodt.avg_cost,2) as avg_cost'),
                    DB::raw('ROUND(sodt.total_additional_cost,2) as total_additional_cost'),
                    DB::raw('ROUND(sodt.profit_avg,2) as profit_avg'),
                  //  'sodt.profit_avg as salePriceIncTax',


                )
                ->leftJoin('tb_item_org as tio', function ($join) {
                    $join->on('sodt.cl_index', '=', 'tio.cl_index')
                        ->where('tio.org_id', '=', '1');
                })
                ->leftJoin('tb_units as tu', function ($join) {
                    $join->on('tio.the_basic_unit', '=', 'tu.id');
                })
                ->get();
            //dump($tableBody);
        }

        // =================================================================



        if ($index_name_id_v == 646) {  //التفصيلي

            if ($tableBody) {

                $sql = "
                insert into sale_order_details_temp(
                    Cl_Item_name,item_supplier_id,serial_id1,selling_price,prime_cost,net_profit,transeaction_type,quantity,unit,profit_avg ,total_additional_cost,details_id,page_id)

                   select item.Cl_Item_name Cl_Item_name,item.item_supplier_id item_supplier_id,
                    som2.serial_id serial_id,

                    sod.quantity_out*price-sod.quantity_out*sod.discountUnit+sod.quantity_out*sod.price*sod.tax_percent/100 selling_price,

                    sod.average_price*sod.quantity_out prime_cost,
                    sod.quantity_out*price-sod.quantity_out*sod.discountUnit+sod.quantity_out*sod.price*sod.tax_percent/100 -sod.average_price*sod.quantity_out- sod.total_additional_cost net_profit,

                    tts.ar_name transeaction_type,
                    sod.quantity_out quantity,
                    tu.ar_name unit,
                   if(sod.average_price*sod.quantity_out=0,100,(sod.quantity_out*price-sod.quantity_out*sod.discountUnit+sod.quantity_out*sod.price*sod.tax_percent/100 -sod.average_price*sod.quantity_out - sod.total_additional_cost)/(sod.average_price*sod.quantity_out)*100) profit_avg,sod.total_additional_cost,som.details_id,som2.page_id

                    from `sale_order_details_tb` sod
                    join sale_order_master_tb som on sod.master_id = som.id and som.page_id = 445 and sod.page_id = 445
                    join tb_item item on sod.Cl_index = item.Cl_index
                    join transaction_type_sales_tb tts on tts.id = som.transeaction_type
                    join tb_units tu on tu.id = sod.unit
                    join sale_order_master_tb som2 on som.details_id = som2.id and som2.page_id = 314 where 1=1 ";

                $sql .= $conditionsGeneral . ";";

                 // dump($sql);
                DB::unprepared(
                    $query = DB::raw($sql)
                );


                $sql = "
                    update sale_order_details_temp sodt
                    join tb_item item on sodt.Cl_index = item.Cl_index
                set
                    sodt.item_supplier_id = item.item_supplier_id,
                    sodt.cl_item_name = item.cl_item_name;
                ";

                // dd($sql);

                DB::unprepared(
                    $query = DB::raw($sql)
                );
            }

           // $tableBody = DB::table('sale_order_details_temp')->get();


            /////////////// View Table  /////////////
            //  Cl_Item_name,item_supplier_id,serial_id1,selling_price,prime_cost,net_profit,transeaction_type,quantity,unit,profit_avg
            $tableBody =  DB::table('sale_order_details_temp as sodt')
                ->select(
                    'sodt.cl_index as control',
                    'sodt.item_supplier_id as item_supplier_id',

                    'sodt.cl_item_name as cl_item_name',
                    'sodt.serial_id1 as serial_id1',
                    'sodt.transeaction_type as transeaction_type',
                    DB::raw('concat(sodt.page_id,"-",sodt.details_id) as control9'),
                    'sodt.unit as unit',

                    DB::raw('ROUND( sodt.quantity,2) as quantity'),
                    DB::raw('ROUND(sodt.selling_price,2 )as salePriceIncTax'),
                    DB::raw('ROUND(sodt.prime_cost,2) as prime_cost'),
                    DB::raw('ROUND(sodt.total_additional_cost,2) as total_additional_cost'),
                    DB::raw('ROUND(sodt.net_profit,2) as net_profit'),
                     DB::raw('ROUND(sodt.profit_avg,2) as profit_avg'),

                )
                ->leftJoin('tb_item_org as tio', function ($join) {
                    $join->on('sodt.cl_index', '=', 'tio.cl_index')
                        ->where('tio.org_id', '=', '1');
                })
                ->leftJoin('tb_units as tu', function ($join) {
                    $join->on('tio.the_basic_unit', '=', 'tu.id');
                })
                ->get();
        }

        if ($tableBody->isNotEmpty()) {

            $tablec = new TableController($pageId);
            if ($index_name_id_v == 646) {
                $tablec->arraySum = [
                     '0' => 'cl_item_name',
                     '1'=>'salePriceIncTax',
                     '2'=>'prime_cost',
                     '3'=>'total_additional_cost',
                     '4' =>'net_profit',
                     //'4'=>'profit_avg',

                    ];   
            }else{
                $tablec->arraySum = [
                     '0' => 'cl_item_name',
                     '1'=>'sale_price_avg',
                     '2'=>'avg_cost',
                     '3'=>'total_additional_cost',
                     '4'=>'profit_avg',


                    ]; 
            }
       //     dump($notInTable);
            $tableHead = $this->tableHeadx($pageId,$tabId,$notInTable);              
            $table1 = $tablec->searchtable($tableHead, $tableBody);

            $newtable = $this->tablepdf($tableHead, $tableBody);

            $response[] = array(
                "table" => '' . $table1 . '',
                "newtable" => $newtable,
            );

        }else{
             $response[] = array(
                "table" => 'لا توجد نتائج',
                "newtable" => '',
            );
        }
            return response()->json($response);
    }


    public function tablepdf($tableHead, $tableBody)
    {
        $arraySum = '';
        $table = '
        <style>
        /*  title */

        #title {
            text-align:center;
            background-color: #f2f2f2;
            color:black; padding-top: 12px;
            padding-bottom: 12px;
          }

        /* New Table  */

        #custom {
            border-collapse: collapse;
            width: 100%;
          }

        #custom td, #custom th {
            border: 1px solid #000;
            padding: 8px;
            font-size: 14px;
            text-align:center;

        }

        </style>
        ';

        $table .= '<h3 id="title" >  كشف مبيعات و سعر التكلفة </h3>'; 
        $table .= '<table id="custom" ><tbody>';
        $table .= '<tr>';

        $table .= '<td style=" background-color: #f2f2f2; color:#000;"> ID  </td>';

        foreach ($tableHead as $head) {
            if ($head->f_in_db  != 'status') {

                $table .= '<td style=" background-color: #f2f2f2; color:#000;">' . $head->name . '</td>';
                $arrHeaders[$head->f_in_db] = $head->f_in_db ;
            }
        }
        $table .= '</tr>';


             $i = 0 ;

        foreach ($tableBody as $items) {
            $i++;
            $table .= '<tr>';
            $table .= '<td style=" background-color: #f2f2f2; color:#000;"> '.$i.' </td>';           
            foreach ($arrHeaders as $key => $item) {
                    $table .= '<td style="text-align:center;">' ;
                        if( $items->$key ==    '0.00' ) $table .= ' ';
                        else $table .= $items->$key ;
                    $table .= '</td>';
            }
            $table .= '</tr>';
        }
        $table .= '</tbody></table>';


        return $table;
    }
}
