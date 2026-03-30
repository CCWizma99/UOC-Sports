
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Equipment</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

  <style>
    @import url("/uoc-sports/public/css/global.css");
    @import url("/uoc-sports/public/css/general/header.css");
    @import url("/uoc-sports/public/css/sports-manager/sub-nav.css");
    @import url("/uoc-sports/public/css/general/footer.css");
    @import url("/uoc-sports/public/css/equipment-manager/equipment.css");
    
  </style>
</head>

<?php
    require "../app/views/templates/general/header.php";
    require "../app/views/equipment-manager/header-subnav.php";
?>
<body>
<div class ="content">

    <div class="card" onclick="window.location.href='/uoc-sports/public/equipment-manager/manage-equipment?sport=Athletics&sport_id=ATH'">
       <img src="/uoc-sports/public/images/equipment/athletic.jpg" class="image">
       <div class="card-content">
           <h3>Athletics</h3>
           <p class="card-id">ID: ATH</p>
       </div>     
    </div>

    
    <div class="card" onclick="window.location.href='/uoc-sports/public/equipment-manager/manage-equipment?sport=Badminton&sport_id=BAD'">
       <img src="/uoc-sports/public/images/equipment/badminton.jpeg" class="image" alt="Badminton">
       <div class="card-content">
           <h3>Badminton</h3>
           <p class="card-id">ID: BAD</p>
       </div>     
    </div>

    
    <div class="card" onclick="window.location.href='/uoc-sports/public/equipment-manager/manage-equipment?sport=Baseball&sport_id=BB'">
       <img src="/uoc-sports/public/images/equipment/baseball.jpg" class="image">
       <div class="card-content">
           <h3>Baseball</h3>
           <p class="card-id">ID: BB</p>
       </div>     
    </div>

    
    <div class="card" onclick="window.location.href='/uoc-sports/public/equipment-manager/manage-equipment?sport=Basketball&sport_id=BAS'">
       <img src="/uoc-sports/public/images/equipment/basketball" class="image">
       <div class="card-content">
           <h3>Basketball</h3>
           <p class="card-id">ID: BAS</p>
       </div>     
    </div>

    
    <div class="card" onclick="window.location.href='/uoc-sports/public/equipment-manager/manage-equipment?sport=Boxing&sport_id=BOX'">
       <img src="/uoc-sports/public/images/equipment/boxing.jpg" class="image">
       <div class="card-content">
           <h3>Boxing</h3>
           <p class="card-id">ID: BOX</p>
       </div>     
    </div>

 
    <div class="card" onclick="window.location.href='/uoc-sports/public/equipment-manager/manage-equipment?sport=Carrom&sport_id=CRM'">
       <img src="/uoc-sports/public/images/equipment/carrom.jpg" class="image">
       <div class="card-content">
           <h3>Carrom</h3>
           <p class="card-id">ID: CRM</p>
       </div>     
    </div>

    <div class="card" onclick="window.location.href='/uoc-sports/public/equipment-manager/manage-equipment?sport=Chess&sport_id=CHE'">
       <img src="/uoc-sports/public/images/equipment/chess.jpg" class="image">
       <div class="card-content">
           <h3>Chess</h3>
           <p class="card-id">ID: CHE</p>
       </div>     
    </div>

    
    <div class="card" onclick="window.location.href='/uoc-sports/public/equipment-manager/manage-equipment?sport=Cricket&sport_id=CRI'">
       <img src="/uoc-sports/public/images/equipment/cricket.jpg" class="image">
       <div class="card-content">
           <h3>Cricket</h3>
           <p class="card-id">ID: CRI</p>
       </div>     
    </div>

    
    <div class="card" onclick="window.location.href='/uoc-sports/public/equipment-manager/manage-equipment?sport=Elle&sport_id=ELL'">
       <img src="/uoc-sports/public/images/equipment/elle.jpg" class="image">
       <div class="card-content">
           <h3>Elle</h3>
           <p class="card-id">ID: ELL</p>
       </div>     
    </div>

    <div class="card" onclick="window.location.href='/uoc-sports/public/equipment-manager/manage-equipment?sport=Football&sport_id=FOO'">
       <img src="/uoc-sports/public/images/equipment/football.jpg" class="image">
       <div class="card-content">
           <h3>Football</h3>
           <p class="card-id">ID: FOO</p>
       </div>     
    </div>

    
    <div class="card" onclick="window.location.href='/uoc-sports/public/equipment-manager/manage-equipment?sport=Hockey&sport_id=HOC'">
       <img src="/uoc-sports/public/images/equipment/hockey.jpg" class="image">
       <div class="card-content">
           <h3>Hockey</h3>
           <p class="card-id">ID: HOC</p>
       </div>     
    </div>
    
    <div class="card" onclick="window.location.href='/uoc-sports/public/equipment-manager/manage-equipment?sport=Kabaddi&sport_id=KBD'">
       <img src="/uoc-sports/public/images/equipment/kabaddi.jpg" class="image">
       <div class="card-content">
           <h3>Kabaddi</h3>
           <p class="card-id">ID: KBD</p>
       </div>     
    </div>

       <div class="card" onclick="window.location.href='/uoc-sports/public/equipment-manager/manage-equipment?sport=Karate&sport_id=KRT'">
       <img src="/uoc-sports/public/images/equipment/karate.jpg" class="image">
       <div class="card-content">
           <h3>Karate</h3>
           <p class="card-id">ID: KRT</p>
       </div>     
    </div>

    
    <div class="card" onclick="window.location.href='/uoc-sports/public/equipment-manager/manage-equipment?sport=Netball&sport_id=NET'">
       <img src="/uoc-sports/public/images/equipment/netball.jpg" class="image">
       <div class="card-content">
           <h3>Netball</h3>
           <p class="card-id">ID: NET</p>
       </div>     
    </div>

    <div class="card" onclick="window.location.href='/uoc-sports/public/equipment-manager/manage-equipment?sport=Rowing&sport_id=ROW'">
       <img src="/uoc-sports/public/images/equipment/rowing.jpg" class="image">
       <div class="card-content">
           <h3>Rowing</h3>
           <p class="card-id">ID: ROW</p>
       </div>     
    </div>

    <div class="card" onclick="window.location.href='/uoc-sports/public/equipment-manager/manage-equipment?sport=Rugby&sport_id=RUG'">
       <img src="/uoc-sports/public/images/equipment/rugby.jpg" class="image">
       <div class="card-content">
           <h3>Rugby</h3>
           <p class="card-id">ID: RUG</p>
       </div>     
    </div>

    <div class="card" onclick="window.location.href='/uoc-sports/public/equipment-manager/manage-equipment?sport=Scrable&sport_id=SCR'">
       <img src="/uoc-sports/public/images/equipment/scrable.jpg" class="image">
       <div class="card-content">
           <h3>Scrable</h3>
           <p class="card-id">ID: SCR</p>
       </div>     
    </div>

    <div class="card" onclick="window.location.href='/uoc-sports/public/equipment-manager/manage-equipment?sport=Swimming&sport_id=SWI'">
       <img src="/uoc-sports/public/images/equipment/swimming.jpeg" class="image" alt="Swimming">
       <div class="card-content">
           <h3>Swimming</h3>
           <p class="card-id">ID: SWI</p>
       </div>     
    </div>

     <div class="card" onclick="window.location.href='/uoc-sports/public/equipment-manager/manage-equipment?sport=Table Tennis&sport_id=TT'">
       <img src="/uoc-sports/public/images/equipment/table_tennis.jpg" class="image">
       <div class="card-content">
           <h3>Table Tennis</h3>
           <p class="card-id">ID: TT</p>
       </div>     
    </div>

    <div class="card" onclick="window.location.href='/uoc-sports/public/equipment-manager/manage-equipment?sport=Taekwondo&sport_id=TKD'">
       <img src="/uoc-sports/public/images/equipment/taekwondo.jpg" class="image">
       <div class="card-content">
           <h3>Taekwondo</h3>
           <p class="card-id">ID: TKD</p>
       </div>     
    </div>

    <div class="card" onclick="window.location.href='/uoc-sports/public/equipment-manager/manage-equipment?sport=Tennis&sport_id=TEN'">
       <img src="/uoc-sports/public/images/equipment/tennis.jpg" class="image">
       <div class="card-content">
           <h3>Tennis</h3>
           <p class="card-id">ID: TNS</p>
       </div>     
    </div>

    <div class="card" onclick="window.location.href='/uoc-sports/public/equipment-manager/manage-equipment?sport=Volleyball&sport_id=VOL'">
       <img src="/uoc-sports/public/images/equipment/volleyball.jpg" class="image">
       <div class="card-content">
           <h3>Volleyball</h3>
           <p class="card-id">ID: VOL</p>
       </div>     
    </div>

    <div class="card" onclick="window.location.href='/uoc-sports/public/equipment-manager/manage-equipment?sport=Weightlifting&sport_id=WL'">
       <img src="/uoc-sports/public/images/equipment/weightlifting.jpg" class="image">
       <div class="card-content">
           <h3>Weightlifting</h3>
           <p class="card-id">ID: WL</p>
       </div>     
    </div>

    
    <div class="card" onclick="window.location.href='/uoc-sports/public/equipment-manager/manage-equipment?sport=Wrestling&sport_id=WRE'">
       <img src="/uoc-sports/public/images/equipment/wrestling.jpg" class="image">
       <div class="card-content">
           <h3>Wrestling</h3>
           <p class="card-id">ID: WRE</p>
       </div>     
    </div>


</div>

<?php
    require "../app/views/templates/general/footer.php";
  
?>
</body>
</html>