// function getGraphConfigData(id) {
//     let rest = '';
//     let config = {
//         method: 'get',
//         maxBodyLength: Infinity,
//         url: 'http://localhost:5050/channel/'+id,
//         headers: { }
//     };
//     return axios.request(config)
//     .then(async (response) => {
//         rest = response.data
//         return await rest
//       })
// }


    
async function getviewsChannelGraph(id1,id2,id3){
    const labels = [
        'Jan',
        'Feb',
        'Mar',
        'Apr',
        'May',
        'Jun',
        'Jul',
        'Aug',
        'Sep',
        'Oct',
        'Nov',
        'Dec'
    ];

    // let channel1 = await getGraphConfigData(id1)
    // let channel2 = await getGraphConfigData(id2)
    // let channel3 = await getGraphConfigData(id3)

    // console.log(channel3)

    
    // const data = {
    //     labels: labels,
    //     datasets: [{
    //     label: channel1.name,
    //     backgroundColor: '#e43834',
    //     borderColor: '#e43834',
    //     data: channel1.views_array,
    //     },{
    //     label: channel2.name,
    //     backgroundColor: '#23a0ed',
    //     borderColor: '#23a0ed',
    //     data: channel2.views_array,
    //     },{
    //         label: channel3.name,
    //         backgroundColor: '#00ff00',
    //         borderColor: '#00ff00',
    //         data: channel3.views_array,
    //     }]
    // };
    const watchers_labels = [
'Jan',
'Feb',
'Mar',
'Apr',
'May',
'Jun',
'Jul',
'Aug',
'Sep',
'Oct',
'Nov',
'Dec',
];

const watchers_data = {
  labels: watchers_labels,
  datasets: [{
    label: 'Views',
    data: [60, 50, 75, 23, 64, 88, 80,46, 65, 50,20,9],
    backgroundColor: [
      '#e4ac87'
    ],
    borderColor: [
      '#e4ac87'
    ],
    borderWidth: 1
  },
  {
    label: 'Subscribed',
    data: [65, 59, 80, 81, 56, 55, 40,56, 55, 40,10,2],
    backgroundColor: [
      '#26a12a'
    ],
    borderColor: [
      '#26a12a'
    ],
    borderWidth: 1
  }]
};

    const config = {
        type: 'line',
        data: watchers_data,
        options: {}
    };

    const followers_config = new Chart(
        document.getElementById('views_chart'),
        config
    );
}

async function getsubsChannelGraph(id1,id2,id3){
    const labels = [
        'Jan',
        'Feb',
        'Mar',
        'Apr',
        'May',
        'Jun',
        'Jul',
        'Aug',
        'Sep',
        'Oct',
        'Nov',
        'Dec'
    ];

    // let channel1 = await getGraphConfigData(id1)
    // let channel2 = await getGraphConfigData(id2)
    // let channel3 = await getGraphConfigData(id3)

    
    const data = {
        labels: labels,
        datasets: [{
            label: 'Views',
            data: [60, 50, 75, 23, 64, 88, 80,46, 65, 50,20,9],
            backgroundColor: [
              '#e4ac87'
            ],
            borderColor: [
              '#e4ac87'
            ],
            borderWidth: 1
          },
          {
            label: 'Subscribed',
            data: [65, 59, 80, 81, 56, 55, 40,56, 55, 40,10,2],
            backgroundColor: [
              '#26a12a'
            ],
            borderColor: [
              '#26a12a'
            ],
            borderWidth: 1
          }]
        // datasets: [{
        // label: channel1.name,
        // backgroundColor: '#e43834',
        // borderColor: '#e43834',
        // data: channel1.subs_array,
        // },{
        // label: channel2.name,
        // backgroundColor: '#23a0ed',
        // borderColor: '#23a0ed',
        // data: channel2.subs_array,
        // },{
        //     label: channel3.name,
        //     backgroundColor: '#00ff00',
        //     borderColor: '#00ff00',
        //     data: channel3.subs_array,
        // }]
    };

    const config = {
        type: 'line',
        data: data,
        options: {}
    };

    const followers_config = new Chart(
        document.getElementById('views_chart'),
        config
    );
}


getviewsChannelGraph('UCq-Fj5jknLsUf-MWSy4_brA','UCFFbwnve3yF62-tVXkTyHqg','UC2pmfLm7iq6Ov1UwYrWYkZA')
getsubsChannelGraph('UCq-Fj5jknLsUf-MWSy4_brA','UCFFbwnve3yF62-tVXkTyHqg','UC2pmfLm7iq6Ov1UwYrWYkZA')