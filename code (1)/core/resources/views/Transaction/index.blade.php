@extends('layouts.user')

@section('content')
<div class="row">
    <div class="col-md-12">
       <div class="panel panel-inverse">
                <div class="panel-heading">
                   <h4 class="panel-title" style="text-align:left;">TRANSACTION</h4>
                </div>
          <div class="panel-body">
        <section id="main-content" class="banner_section dashboard">
        <section class="wrapper">
        <div class="row dashboard_row">
        <div class="col-lg-12 main-chart">
            <!--CUSTOM CHART START -->
           <div class="col-12">
                <table class="table table-hover table-bordered text-light">
                    <thead class="thead-success">
                       <th>S.NO</th>
                       <th>AMOUNT</th>
                       <th>TASK</th>
                        <th>BY/{{$gnl->cur}}</th>
                       <th>TRANSACTION ID</th>
                        <th>STATUS</th>
                       <th>DATE/TIME</th>
                    </tr></thead>
                    <tbody>
                      <?php $count=1; ?>
                       @foreach($history as $historys)
                        <tr>
                        <td><?php echo $count;?></td>
                        <td><?php echo $historys->amount;?></td>
                        <td><?php echo $historys->task;?></td>
                        <td><?php echo $historys->item;?></td>
                        <?php if($historys->hash=="-"){?>
                        <td>-</td>
                           <?php }else{?>
                             <td><?php echo $historys->hash;?></td>
                           <?php } 
                          ?>
                        
                        <td><?php echo $historys->status;?></td>
                        <td><?php echo $historys->time;?></td>
                        </tr>
                         <?php $count++;?>
                         @endforeach
                        
                        </tbody>
                </table>
                <?php echo $history->render(); ?>
            </div>
            </div>
            </div>
            </section>
            </section>
            </div>
            </div>
            </div>



@endsection