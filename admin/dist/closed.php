<?php
session_start();
include "includes/dao.php";
include "includes/header.php";
include "includes/navbar.php";
include "includes/DBConfig.php";
include "includes/admin_model.php";

$admin = new admin_model();
$id = $_SESSION['user_id'];
$tickets = $admin->closedTickets($id);
$admin->setOverdue();

$_SESSION['start_date'] = '';
$_SESSION['end_date'] = '';

if(isset($_POST['submit']))
{
    $from = $_POST['start'];
    $to= $_POST['end'];
    $start = date('d M Y H:i:s', strtotime($from));
    $end = date('d M Y H:i:s', strtotime($to));
    $tickets= $admin->getClosedReport($from, $to);
    $_SESSION['start_date'] = $from;
    $_SESSION['end_date'] = $to;
}

?>
<div id="layoutSidenav_content">
             <main>
                   
                <div class="container-fluid">
                    <br>
                  <div class="card mb-4">
                         <div class="card-header">
                         <i class="fas fa-book-open"></i>
                                Filter
                            </div>
                            <div class="card-body">
                        <form method='POST' class='form-horizontal'>
                        <div class="form-row" >
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="small mb-1" for="fault_type">Start Date</label>
                                    <input class="form-control" name="start" value="<?=$_SESSION['start_date']?>" id="start" type="datetime-local"  required>
                                 </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="small mb-1" for="sla">End Date</label>
                                    <input class="form-control" name="end" value="<?=$_SESSION['end_date']?>" id="end" type="datetime-local" required>
                                 </div>

                                
                            </div>
                        
                         </div>

                         <div class="form-group" >
                            <input class="btn btn-secondary"  id="submit" type="submit" name="submit" value="Filter Data" >
                        </div>

                         </form>
                    </div>
                 </div>

                    <div class="card mb-4">
                         <div class="card-header" >
                                <i class="fas fa-table mr-1"></i>
                                Closed Tickets
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                        <thead>
                                        <?php $style = 'style="font-size: 12px";';?>
                                        <tr <?php echo $style;?>>
                                                <th>Tracking No</th>
                                                <th>User</th>
                                                <th>Title</th>
                                                <th>Fault</th>
                                                <th>IT Officer</th>
                                                <th>SLA (Mins)</th>
                                                <th>Reported</th>
                                                <th>Time Taken(Mins)</th>
                                                <th>Closed</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tfoot>
                                        <tr <?php echo $style;?>>
                                                <th>Tracking No</th>
                                                <th>User</th>
                                                <th>Title</th>
                                                <th>Fault</th>
                                                <th>IT Officer</th>
                                                <th>SLA (Mins)</th>
                                                <th>Reported</th>
                                                <th>Time Taken(Mins)</th>
                                                <th>Closed</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </tfoot>
                                        <tbody>
                                        <?php 
                                          while ($row = $tickets->fetch(PDO::FETCH_ASSOC)) {
                                            $fault_time = new DateTime($row['fault_time']);
                                            $closed = new DateTime($row['date_closed']);
                                            $stamp =  $closed->diff($fault_time)->format("%i");
                                            $FT = $fault_time->format('d M Y H:i:s');
                                            $CT = $closed->format('d M Y H:i:s');

                                            $minutes = $stamp->days * 24 * 60;
                                            $minutes += $stamp->h * 60;
                                            $minutes += $stamp->i;

                                            if($minutes >= 60)
                                            {
                                                $minutes =  round(abs($minutes / 60)).' Hrs';
                                            }
                                            else if($minutes >= 1440)
                                            {
                                                $minutes =  round(abs($minutes / 1440)).' Days';
                                            }
                                            else 
                                            {
                                                $minutes =   round($minutes).' Mins';
                                            }
                                        ?>
                                            <tr <?php echo $style;?>>
                                                <td><?=$row['ticket_id']?></td>
                                                <td><?=$row['name']?></td>
                                                <td><?=$row['job_title']?></td>
                                                <td><?=$row['ticket_type']?></td>
                                                <td><?=$row['staff']?></td>
                                                <td><?=$row['ticket_sla']?></td>
                                                <td><?=$FT?></td>
                                                <td><?=$minutes?></td>
                                                <td><?=$CT?></td>
                                                <td><?=$row['ticket_status']?></td>
                                                <td align="center">
                                                    <a title="View " href="view_closed.php?id=<?=$row['ticket_id']?>" <?php echo $style;?>  class=" btn-sm btn-success">Details</a>
                                                </td>
                                            </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                    <form method="POST">
                                        <div class="form-group">
                                            <button class="btn btn-success" id="export_data" name="export_data" style="margin-left: 40%;">Export To Excel</button>
                                        </div>
                                    </form>
                                </div>
                            </div>                            

                            
                    </div>


                </div>
            </main>

<?php  
    include 'includes/footer1.php';
?>
