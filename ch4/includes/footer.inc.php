<div id="footer">
    <p>&copy; 
        <?php 
        $startYear = 2013;
        $currentYear = date('Y');
        if($currentYear == $startYear){
            echo $startYear;
        }else{
            echo "{$startYear}&#8211;{$currentYear}";
        }


        ?> 
        David Powers</p>
</div>